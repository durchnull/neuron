<?php

namespace App\Http\Controllers\Engine;

use App\Http\Resources\Engine\CartRuleResource;
use App\Models\Engine\CartRule;

class CartRuleController extends EngineResourceController
{

    public static function getModelClass(): string
    {
        return CartRule::class;
    }

    public static function getResourceClass(): string
    {
        return CartRuleResource::class;
    }
}
