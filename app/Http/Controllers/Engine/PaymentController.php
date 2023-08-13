<?php

namespace App\Http\Controllers\Engine;

use App\Http\Resources\Engine\PaymentResource;
use App\Models\Engine\Payment;

class PaymentController extends EngineResourceController
{

    public static function getModelClass(): string
    {
        return Payment::class;
    }

    public static function getResourceClass(): string
    {
        return PaymentResource::class;
    }
}
