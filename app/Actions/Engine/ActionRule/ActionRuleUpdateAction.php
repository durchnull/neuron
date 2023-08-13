<?php

namespace App\Actions\Engine\ActionRule;

class ActionRuleUpdateAction extends ActionRuleAction
{

    public static function rules(): array
    {
        return [
            'condition_id' => 'nullable|uuid|exists:conditions,id',
            'name' => 'nullable|string',
            // @todo
            //'action' => 'nullable|string|in:' . implode(',', Rule::allowedActionRuleClasses()),
            'action' => 'nullable|string',
            'enabled' => 'nullable|boolean',
        ];
    }

    protected function gate(array $attributes): void
    {
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
