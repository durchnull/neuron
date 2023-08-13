<?php

namespace App\Services\Integration\PaymentProvider\Resources;

use App\Customer\CustomerProfile;
use App\Enums\Address\SalutationEnum;
use App\Enums\Transaction\TransactionStatusEnum;
use Exception;
use Illuminate\Support\Str;

class AmazonPayCheckoutSessionResource extends Resource
{
    public function __construct(protected mixed $resource, array $data = [])
    {
        parent::__construct($resource, $data);

        $this->data = array_filter(array_merge($data, [
            'chargePermissionId' => $this->resource['chargePermissionId'] ?? null,
            'chargeId' => $this->resource['chargeId'] ?? null,
        ]));
    }

    public function getId(): string
    {
        return $this->resource['checkoutSessionId'];
    }

    public function getCheckoutUrl(): string
    {
        return $this->resource['webCheckoutDetails']['amazonPayRedirectUrl'];
    }

    public function getPaymentMethod(): string
    {
        // @todo multiple entries possible?
        return $this->resource['paymentPreferences'][0]['paymentDescriptor'];
    }

    /**
     * @throws Exception
     */
    public function getStatus(): TransactionStatusEnum
    {
        $checkoutSessionState = $this->resource['statusDetails']['state'];

        if ($checkoutSessionState === 'Open') {
            return TransactionStatusEnum::Created;
        } elseif ($checkoutSessionState === 'Completed') {
            if (isset($this->resource['chargePermissionId'], $this->resource['chargeId'])) {
                return TransactionStatusEnum::Authorized;
            } else {
                // @todo
                throw new Exception('Completed but missing charge');
            }
        } elseif ($checkoutSessionState === 'Canceled') {
            return TransactionStatusEnum::Canceled;
        }

        throw new Exception("State [$checkoutSessionState] not implemented");
    }

    public function close(): void
    {
    }

    public function refund(): void
    {
    }

    public function place(): void
    {
        // TODO: Implement place() method.
    }

    public function getCustomerProfile(): ?CustomerProfile
    {
        try {
            return new CustomerProfile([
                'email' => $this->resource['buyer']['email'],
                'phone' => $this->resource['buyer']['phoneNumber'],
                'note' => null,
                'shipping_address' => $this->makeAddress($this->resource['shippingAddress']),
                'billing_address' => $this->makeAddress($this->resource['billingAddress'])
            ]);
        } catch (Exception $exception) {
            return null;
        }
    }

    protected function makeAddress(array $address): array
    {
        $company = !empty($address['addressLine1']) && !empty($address['addressLine2'])
            ? $address['addressLine1']
            : null;

        $firstName = trim(Str::before($address['name'], ' '));
        $lastName = trim(Str::after($address['name'], $firstName));

        $addressAndStreet = $address['addressLine1'] ?? $address['addressLine2'] ?? $address['addressLine3'];

        $street = trim(Str::beforeLast($addressAndStreet, ' '));
        $number = trim(Str::after($addressAndStreet, $street));

        return [
            'company' => $company,
            'salutation' => SalutationEnum::None->value,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'street' => $street,
            'number' => $number,
            'additional' => $address['addressLine3'],
            'postal_code' => $address['postalCode'],
            'city' => $address['city'],
            'country_code' => $address['countryCode'],
        ];
    }
}
