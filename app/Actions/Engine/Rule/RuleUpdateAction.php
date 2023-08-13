<?php

namespace App\Actions\Engine\Rule;

class RuleUpdateAction extends RuleAction
{

    public static function rules(): array
    {
        return [
            'condition_id' => 'nullable|uuid|exists:conditions,id',
            'name' => 'nullable|string',
            'consequences' => 'nullable|array',
            'position' => 'nullable|integer|min:0',
            'enabled' => 'nullable|bool',
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
