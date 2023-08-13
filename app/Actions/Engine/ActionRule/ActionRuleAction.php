<?php

namespace App\Actions\Engine\ActionRule;

use App\Actions\Action;
use App\Models\Engine\ActionRule;

abstract class ActionRuleAction extends Action
{
    final public static function targetClass(): string
    {
        return ActionRule::class;
    }
}
