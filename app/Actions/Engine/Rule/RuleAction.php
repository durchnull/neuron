<?php

namespace App\Actions\Engine\Rule;

use App\Actions\Action;
use App\Models\Engine\Rule;

abstract class RuleAction extends Action
{
    final public static function targetClass(): string
    {
        return Rule::class;
    }
}
