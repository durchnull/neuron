<?php

namespace App\Http\Controllers\Engine;

use App\Http\Resources\Engine\MerchantResource;
use App\Models\Engine\Merchant;

class MerchantController extends EngineResourceController
{

    public static function getModelClass(): string
    {
        return Merchant::class;
    }

    public static function getResourceClass(): string
    {
        return MerchantResource::class;
    }
}
