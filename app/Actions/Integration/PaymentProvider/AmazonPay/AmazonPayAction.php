<?php

namespace App\Actions\Integration\PaymentProvider\AmazonPay;

use App\Actions\Action;
use App\Models\Integration\PaymentProvider\AmazonPay;

abstract class AmazonPayAction extends Action
{
    final public static function targetClass(): string
    {
        return AmazonPay::class;
    }
}
