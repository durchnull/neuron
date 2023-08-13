<?php

namespace App\Actions\Engine\CartRule;

class CartRuleUpdateAction extends CartRuleAction
{
    public static function rules(): array
    {
        return [
            'rule_id' => 'nullable|uuid|exists:rules,id',
            'name' => 'nullable|string',
            'enabled' => 'nullable|boolean',
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();

        // @todo [action] remove relationship from carts if enabled = false ?
    }
}
