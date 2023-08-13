<?php

namespace App\Actions\Engine\Product;

use App\Actions\Action;
use App\Models\Engine\Product;

abstract class ProductAction extends Action
{
    final public static function targetClass(): string
    {
        return Product::class;
    }
}
