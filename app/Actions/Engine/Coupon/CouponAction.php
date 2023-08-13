<?php

namespace App\Actions\Engine\Coupon;

use App\Actions\Action;
use App\Models\Engine\Coupon;

abstract class CouponAction extends Action
{
    final public static function targetClass(): string
    {
        return Coupon::class;
    }
}
