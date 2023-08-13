<?php

namespace App\Http\Controllers\Engine;

use App\Http\Resources\Engine\ActionRuleResource;
use App\Models\Engine\ActionRule;

class ActionRuleController extends EngineResourceController
{
    public static function getModelClass(): string
    {
        return ActionRule::class;
    }

    public static function getResourceClass(): string
    {
        return ActionRuleResource::class;
    }
}
