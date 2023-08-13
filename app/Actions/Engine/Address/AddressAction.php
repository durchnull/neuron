<?php

namespace App\Actions\Engine\Address;

use App\Actions\Action;
use App\Models\Engine\Address;

abstract class AddressAction extends Action
{
    final public static function targetClass(): string
    {
        return Address::class;
    }
}
