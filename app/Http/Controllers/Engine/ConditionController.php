<?php

namespace App\Http\Controllers\Engine;

use App\Http\Resources\Engine\ConditionResource;
use App\Models\Engine\Condition;

class ConditionController extends EngineResourceController
{

    public static function getModelClass(): string
    {
        return Condition::class;
    }

    public static function getResourceClass(): string
    {
        return ConditionResource::class;
    }
}
