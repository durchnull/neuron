<?php

namespace App\Http\Controllers\Integration\PaymentProvider;

use App\Http\Controllers\ResourceController;

abstract class PaymentProviderController extends ResourceController
{
    public static function getActionNamespace(): string
    {
        return 'App\Actions\Integration\PaymentProvider';
    }
}
