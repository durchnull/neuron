<?php

namespace App\Actions\Engine\CartRule;

use Exception;

class CartRuleCreateAction extends CartRuleAction
{

    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid|exists:sales_channels,id',
            'rule_id' => 'required|uuid|exists:rules,id',
            'name' => 'required|string',
            'enabled' => 'required|boolean',
        ];
    }

    /**
     * @throws Exception
     */
    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
