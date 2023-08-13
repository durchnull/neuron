<?php

namespace App\Actions\Engine\CartRule;

use App\Actions\Action;
use App\Models\Engine\CartRule;

abstract class CartRuleAction extends Action
{
    final public static function targetClass(): string
    {
        return CartRule::class;
    }
}
