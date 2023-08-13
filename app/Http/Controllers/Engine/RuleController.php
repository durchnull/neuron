<?php

namespace App\Http\Controllers\Engine;

use App\Http\Resources\Engine\RuleResource;
use App\Models\Engine\Rule;

class RuleController extends EngineResourceController
{
    public static function getModelClass(): string
    {
        return Rule::class;
    }

    public static function getResourceClass(): string
    {
        return RuleResource::class;
    }
}
