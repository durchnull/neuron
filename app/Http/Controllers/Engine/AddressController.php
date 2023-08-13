<?php

namespace App\Http\Controllers\Engine;

use App\Http\Resources\Engine\AddressResource;
use App\Models\Engine\Address;

class AddressController extends EngineResourceController
{

    public static function getModelClass(): string
    {
        return Address::class;
    }

    public static function getResourceClass(): string
    {
        return AddressResource::class;
    }
}
