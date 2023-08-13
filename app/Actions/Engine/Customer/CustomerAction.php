<?php

namespace App\Actions\Engine\Customer;

use App\Actions\Action;
use App\Models\Engine\Customer;

abstract class CustomerAction extends Action
{
    final public static function targetClass(): string
    {
        return Customer::class;
    }
}
