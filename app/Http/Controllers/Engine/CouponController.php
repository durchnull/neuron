<?php

namespace App\Http\Controllers\Engine;

use App\Http\Resources\Engine\CouponResource;
use App\Models\Engine\Coupon;

class CouponController extends EngineResourceController
{

    public static function getModelClass(): string
    {
        return Coupon::class;
    }

    public static function getResourceClass(): string
    {
        return CouponResource::class;
    }
}
