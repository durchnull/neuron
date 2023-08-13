<?php

namespace App\Facades;

use App\Models\Engine\Order;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string generateCode()
 * @method static \App\Models\Engine\Coupon|null getByCode(string $code)
 * @method static Order chargeCredit(Order $order)
 *
 * @see CouponService
 */
class Coupon extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'coupon';
    }
}
