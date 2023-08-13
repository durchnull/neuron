<?php

namespace App\Services\Engine;

use App\Actions\Engine\Address\AddressCreateAction;
use App\Actions\Engine\Customer\CustomerCreateAction;
use App\Actions\Engine\Order\OrderUpdateCustomerAction;
use App\Contracts\Engine\CustomerServiceContract;
use App\Contracts\Engine\SalesChannelContract;
use App\Customer\CustomerProfile;
use App\Enums\TriggerEnum;
use App\Exceptions\Order\PolicyException;
use App\Models\Engine\Address;
use App\Models\Engine\Customer;
use App\Models\Engine\Order;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CustomerService implements CustomerServiceContract
{
    public function __construct(protected SalesChannelContract $salesChannelService)
    {
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     */
    public function searchOrCreate(CustomerProfile $customerProfile): Customer
    {
        return $this->search($customerProfile) ?? $this->create($customerProfile);
    }

    public function search(CustomerProfile $customerProfile): ?Customer
    {
        return Customer::where('sales_channel_id', $this->salesChannelService->id())
            ->where(function ($query) use ($customerProfile) {
                $query->where('email', $customerProfile->email)
                    ->orWhere(function (Builder $query) use ($customerProfile) {
                        $query->whereNotNull('phone')
                            ->where('phone', $customerProfile->phone);
                    });
            })
            ->first();
    }

    /**
     * @param  CustomerProfile  $customerProfile
     * @return Customer
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function create(CustomerProfile $customerProfile): Customer
    {
        $action = new CustomerCreateAction(
            new Customer(),
            [
                'sales_channel_id' => $this->salesChannelService->id(),
                'email' => $customerProfile->email,
                'full_name' => $customerProfile->fullName,
                'phone' => $customerProfile->phone
            ],
            TriggerEnum::App
        );

        $action->trigger();

        return $action->target();
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     */
    public function searchOrCreateBillingAddress(Customer $customer, CustomerProfile $customerProfile): Address
    {
        /** @var Address|null $latestAddress */
        $latestAddress = Address::where([
            'sales_channel_id' => $this->salesChannelService->id(),
            'street' => $customerProfile->billingStreet,
            'number' => $customerProfile->billingNumber,
            'postal_code' => $customerProfile->billingPostalCode,
            'country_code' => $customerProfile->billingCountryCode,
        ])
            ->latest('updated_at')
            ->first();

        if ($latestAddress) {
            return $latestAddress;
        }

        return $this->createBillingAddress($customer, $customerProfile);
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     */
    public function searchOrCreateShippingAddress(Customer $customer, CustomerProfile $customerProfile): Address
    {
        /** @var Address|null $latestAddress */
        $latestAddress = Address::where([
            'sales_channel_id' => $this->salesChannelService->id(),
            'customer_id' => $customer->id,
            'street' => $customerProfile->shippingStreet,
            'number' => $customerProfile->shippingNumber,
            'postal_code' => $customerProfile->shippingPostalCode,
            'country_code' => $customerProfile->shippingCountryCode,
        ])
            ->latest('updated_at')
            ->first();

        if ($latestAddress) {
            return $latestAddress;
        }

        return $this->createShippingAddress($customer, $customerProfile);
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    public function createBillingAddress(Customer $customer, CustomerProfile $customerProfile): ?Address
    {
        if ($customerProfile->hasBillingAddress()) {
            $addressCreateAction = new AddressCreateAction(new Address(), [
                'sales_channel_id' => $this->salesChannelService->id(),
                'customer_id' => $customer->id,
                'company' => $customerProfile->billingCompany,
                'salutation' => $customerProfile->billingSalutation,
                'first_name' => $customerProfile->billingFirstName,
                'last_name' => $customerProfile->billingLastName,
                'street' => $customerProfile->billingStreet,
                'number' => $customerProfile->billingNumber,
                'additional' => $customerProfile->billingAdditional,
                'postal_code' => $customerProfile->billingPostalCode,
                'city' => $customerProfile->billingCity,
                'country_code' => $customerProfile->billingCountryCode,
            ], TriggerEnum::App);

            $addressCreateAction->trigger();

            return $addressCreateAction->target();
        }

        return null;
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    public function createShippingAddress(Customer $customer, CustomerProfile $customerProfile): Address
    {
        $addressCreateAction = new AddressCreateAction(new Address(), [
            'sales_channel_id' => $this->salesChannelService->id(),
            'customer_id' => $customer->id,
            'company' => $customerProfile->shippingCompany,
            'salutation' => $customerProfile->shippingSalutation,
            'first_name' => $customerProfile->shippingFirstName,
            'last_name' => $customerProfile->shippingLastName,
            'street' => $customerProfile->shippingStreet,
            'number' => $customerProfile->shippingNumber,
            'additional' => $customerProfile->shippingAdditional,
            'postal_code' => $customerProfile->shippingPostalCode,
            'city' => $customerProfile->shippingCity,
            'country_code' => $customerProfile->shippingCountryCode,
        ], TriggerEnum::App);

        $addressCreateAction->trigger();

        return $addressCreateAction->target();
    }

    public function setNew(Customer $customer, bool $value): Customer
    {
        $customer->update([
            'new' => $value
        ]);

        return $customer;
    }


    /**
     * @param  Order  $order
     * @param  CustomerProfile  $customerProfile
     * @return mixed
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function updateOrder(Order $order, CustomerProfile $customerProfile): Order
    {
        $customer = $this->searchOrCreate($customerProfile);
        $shippingAddress = $this->searchOrCreateShippingAddress($customer, $customerProfile);
        $billingAddress = $customerProfile->hasBillingAddress()
            ? $this->searchOrCreateBillingAddress($customer, $customerProfile)
            : $shippingAddress;

        $action = new OrderUpdateCustomerAction(
            $order,
            [
                'order_id' => $order->id,
                'customer_id' => $customer->id,
                'billing_address_id' => $billingAddress->id,
                'shipping_address_id' => $shippingAddress->id,
                'customer_note' => $customerProfile->note
            ],
            TriggerEnum::Api
        );

        $action->trigger();

        return $action->target();
    }
}
