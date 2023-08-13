<?php

namespace App\Actions\Engine\Condition;

use App\Actions\Action;
use App\Models\Engine\Condition;

abstract class ConditionAction extends Action
{
    final public static function targetClass(): string
    {
        return Condition::class;
    }
}
