<?php

namespace App\Actions\Integration\PaymentProvider\NeuronPayment;

use App\Actions\Action;
use App\Models\Integration\PaymentProvider\NeuronPayment;

abstract class NeuronPaymentAction extends Action
{
    final public static function targetClass(): string
    {
        return NeuronPayment::class;
    }
}
