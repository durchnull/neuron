<?php

namespace App\Facades;

use App\Customer\CustomerProfile;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Shipping getByGuessing(array $attributes)
 * @method static Shipping getByCountryCode(string $countryCode)
 * @method static array getCountryCodes()
 * @method static \App\Models\Engine\Order updateOrder(\App\Models\Engine\Order $order, CustomerProfile $customerProfile)
 *
 * @see ShippingService
 */
class Shipping extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'shipping';
    }
}
