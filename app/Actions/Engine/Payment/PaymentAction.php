<?php

namespace App\Actions\Engine\Payment;

use App\Actions\Action;
use App\Models\Engine\Payment;

abstract class PaymentAction extends Action
{
    final public static function targetClass(): string
    {
        return Payment::class;
    }
}
