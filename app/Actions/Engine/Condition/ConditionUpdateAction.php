<?php

namespace App\Actions\Engine\Condition;

class ConditionUpdateAction extends ConditionAction
{
    public static function rules(): array
    {
        return [
            'name' => 'nullable|string',
            'collection' => 'nullable|array',
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
