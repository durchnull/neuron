<?php

namespace App\Http\Controllers\Engine;

use App\Http\Resources\Engine\VatResource;
use App\Models\Engine\Vat;

class VatController extends EngineResourceController
{
    public static function getModelClass(): string
    {
        return Vat::class;
    }

    public static function getResourceClass(): string
    {
        return VatResource::class;
    }
}
