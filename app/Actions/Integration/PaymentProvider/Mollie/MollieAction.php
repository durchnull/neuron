<?php

namespace App\Actions\Integration\PaymentProvider\Mollie;

use App\Actions\Action;
use App\Models\Integration\PaymentProvider\Mollie;

abstract class MollieAction extends Action
{
    final public static function targetClass(): string
    {
        return Mollie::class;
    }
}
