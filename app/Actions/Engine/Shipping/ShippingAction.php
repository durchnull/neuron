<?php

namespace App\Actions\Engine\Shipping;

use App\Actions\Action;
use App\Models\Engine\Shipping;

abstract class ShippingAction extends Action
{
    final public static function targetClass(): string
    {
        return Shipping::class;
    }
}
