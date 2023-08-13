<?php

namespace App\Http\Controllers\Engine;

use App\Http\Resources\Engine\CustomerResource;
use App\Models\Engine\Customer;

class CustomerController extends EngineResourceController
{

    public static function getModelClass(): string
    {
        return Customer::class;
    }

    public static function getResourceClass(): string
    {
        return CustomerResource::class;
    }
}
