<?php

namespace App\Http\Controllers\Engine;

use App\Http\Resources\Engine\ShippingResource;
use App\Models\Engine\Shipping;

class ShippingController extends EngineResourceController
{

    public static function getModelClass(): string
    {
        return Shipping::class;
    }

    public static function getResourceClass(): string
    {
        return ShippingResource::class;
    }
}
