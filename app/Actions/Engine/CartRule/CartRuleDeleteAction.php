<?php

namespace App\Actions\Engine\CartRule;

class CartRuleDeleteAction extends CartRuleAction
{
    public static function rules(): array
    {
        return [];
    }

    protected function gate(array $attributes): void
    {
        // @todo doesnt belong to any order

        // rule > condition
    }

    protected function apply(): void
    {
        $this->target->delete();
    }
}
