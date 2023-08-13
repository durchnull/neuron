<?php

namespace App\Http\Controllers\Engine;

use App\Http\Resources\Engine\ProductPriceResource;
use App\Models\Engine\ProductPrice;

class ProductPriceController extends EngineResourceController
{
    public static function getModelClass(): string
    {
        return ProductPrice::class;
    }

    public static function getResourceClass(): string
    {
        return ProductPriceResource::class;
    }
}
