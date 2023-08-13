<?php

namespace App\Customer;

use App\Enums\Address\SalutationEnum;

class CustomerProfile
{
    // customer
    public string $email;
    public string $fullName;
    public ?string $phone;
    public ?string $note;
    public ?string $shippingCompany;
    public string $shippingSalutation;
    public string $shippingFirstName;
    public string $shippingLastName;
    public string $shippingStreet;
    public string $shippingNumber;
    public ?string $shippingAdditional;
    public string $shippingPostalCode;
    public string $shippingCity;
    public string $shippingCountryCode;
    public ?string $billingCompany;

    public ?string $billingSalutation;
    public ?string $billingFirstName;
    public ?string $billingLastName;
    public ?string $billingStreet;
    public ?string $billingNumber;
    public ?string $billingAdditional;
    public ?string $billingPostalCode;
    public ?string $billingCity;
    public ?string $billingCountryCode;

    public function __construct(array $attributes)
    {
        $this->email = $attributes['email'];
        $this->phone = $attributes['phone'] ?? null;
        $this->note = $attributes['note'] ?? null;

        $this->shippingCompany = $attributes['shipping_address']['company'] ?? null;
        $this->shippingSalutation = $attributes['shipping_address']['salutation'] ?? SalutationEnum::None->value;
        $this->shippingFirstName = $attributes['shipping_address']['first_name'];
        $this->shippingLastName = $attributes['shipping_address']['last_name'];
        $this->shippingStreet = $attributes['shipping_address']['street'];
        $this->shippingNumber = $attributes['shipping_address']['number'];
        $this->shippingAdditional = $attributes['shipping_address']['additional'] ?? null;
        $this->shippingPostalCode = $attributes['shipping_address']['postal_code'];
        $this->shippingCity = $attributes['shipping_address']['city'];
        $this->shippingCountryCode = $attributes['shipping_address']['country_code'];

        $this->billingCompany = $attributes['billing_address']['company'] ?? null;
        $this->billingSalutation = $attributes['billing_address']['salutation'] ?? null;
        $this->billingFirstName = $attributes['billing_address']['first_name'] ?? null;
        $this->billingLastName = $attributes['billing_address']['last_name'] ?? null;
        $this->billingStreet = $attributes['billing_address']['street'] ?? null;
        $this->billingNumber = $attributes['billing_address']['number'] ?? null;
        $this->billingAdditional = $attributes['billing_address']['additional'] ?? null;
        $this->billingPostalCode = $attributes['billing_address']['postal_code'] ?? null;
        $this->billingCity = $attributes['billing_address']['city'] ?? null;
        $this->billingCountryCode = $attributes['billing_address']['country_code'] ?? null;

        $this->fullName = ($this->billingFirstName ?? $this->shippingFirstName) . ' ' . ($this->billingLastName ?? $this->shippingLastName);
    }

    public function hasBillingAddress(): bool
    {
        return $this->billingFirstName !== null &&
            $this->billingLastName !== null &&
            $this->billingStreet !== null &&
            $this->billingNumber !== null &&
            $this->billingPostalCode !== null &&
            $this->billingCity !== null &&
            $this->billingCountryCode !== null;
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'phone' => $this->phone,
            'note' => $this->note,
            'shipping_address' => [
                'company' => $this->shippingCompany,
                'salutation' => $this->shippingSalutation,
                'first_name' => $this->shippingFirstName,
                'last_name' => $this->shippingLastName,
                'street' => $this->shippingStreet,
                'number' => $this->shippingNumber,
                'additional' => $this->shippingAdditional,
                'postal_code' => $this->shippingPostalCode,
                'city' => $this->shippingCity,
                'country_code' => $this->shippingCountryCode,
            ],
            'billing_address' => [
                'company' => $this->billingCompany,
                'salutation' => $this->billingSalutation,
                'first_name' => $this->billingFirstName,
                'last_name' => $this->billingLastName,
                'street' => $this->billingStreet,
                'number' => $this->billingNumber,
                'additional' => $this->billingAdditional,
                'postal_code' => $this->billingPostalCode,
                'city' => $this->billingCity,
                'country_code' => $this->billingCountryCode,
            ]
        ];
    }
}
