<?php

namespace App\Contracts\Engine;


use App\Models\Engine\Address;
use App\Models\Engine\Customer;
use App\Customer\CustomerProfile;
use App\Models\Engine\Order;

interface CustomerServiceContract
{
    public function searchOrCreate(CustomerProfile $customerProfile): Customer;

    public function search(CustomerProfile $customerProfile): ?Customer;

    public function create(CustomerProfile $customerProfile): Customer;

    public function createBillingAddress(Customer $customer, CustomerProfile $customerProfile): ?Address;

    public function createShippingAddress(Customer $customer, CustomerProfile $customerProfile): Address;

    public function searchOrCreateBillingAddress(\App\Models\Engine\Customer $customer, CustomerProfile $customerProfile): Address;

    public function searchOrCreateShippingAddress(\App\Models\Engine\Customer $customer, CustomerProfile $customerProfile): Address;

    public function setNew(Customer $customer, bool $value): Customer;

    public function updateOrder(Order $order, CustomerProfile $customerProfile);
}
