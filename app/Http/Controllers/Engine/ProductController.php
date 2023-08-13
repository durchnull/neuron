<?php

namespace App\Http\Controllers\Engine;

use App\Http\Resources\Engine\ProductResource;
use App\Models\Engine\Product;

class ProductController extends EngineResourceController
{
    public static function getModelClass(): string
    {
        return Product::class;
    }

    public static function getResourceClass(): string
    {
        return ProductResource::class;
    }
}
