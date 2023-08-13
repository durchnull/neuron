<?php

namespace App\Actions\Integration\PaymentProvider\Paypal;

use App\Actions\Action;
use App\Models\Integration\PaymentProvider\Paypal;

abstract class PaypalAction extends Action
{
    final public static function targetClass(): string
    {
        return Paypal::class;
    }
}
