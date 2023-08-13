<?php

namespace App\Facades;

use App\Models\Engine\Address;
use App\Customer\CustomerProfile;
use Cassandra\Custom;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Models\Engine\Customer searchOrCreate(CustomerProfile $customerProfile)
 * @method static Address searchOrCreateBillingAddress(\App\Models\Engine\Customer $customer, CustomerProfile $customerProfile)
 * @method static Address searchOrCreateShippingAddress(\App\Models\Engine\Customer $customer, CustomerProfile $customerProfile)
 * @method static \App\Models\Engine\Customer setNew(\App\Models\Engine\Customer $customer, bool $value)
 * @method static \App\Models\Engine\Order updateOrder(\App\Models\Engine\Order $order, CustomerProfile $customerProfile)
 *
 * @see CustomerService
 */
class Customer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'customer';
    }
}
