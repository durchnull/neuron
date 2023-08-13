<?php

namespace App\Actions\Engine\Vat;

use App\Actions\Action;
use App\Models\Engine\Vat;

abstract class VatAction extends Action
{
    final public static function targetClass(): string
    {
        return Vat::class;
    }
}
