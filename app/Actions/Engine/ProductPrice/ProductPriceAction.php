<?php

namespace App\Actions\Engine\ProductPrice;

use App\Actions\Action;
use App\Models\Engine\ProductPrice;

abstract class ProductPriceAction extends Action
{
    final public static function targetClass(): string
    {
        return ProductPrice::class;
    }

    protected function gate(array $attributes): void
    {
        // @todo no overlapping
    }
}
