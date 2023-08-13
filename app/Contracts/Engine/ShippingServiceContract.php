<?php

namespace App\Contracts\Engine;

use App\Customer\CustomerProfile;
use App\Models\Engine\Order;
use App\Models\Engine\Shipping;

interface ShippingServiceContract
{
    public function getByGuessing(array $attributes): ?Shipping;

    public function getByCountryCode(string $countryCode): ?Shipping;

    public function getCountryCodes(): array;

    public function updateOrder(Order $order, CustomerProfile $customerProfile): Order;
}
