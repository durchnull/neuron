<?php

namespace App\Actions\Engine\ActionRule;

class ActionRuleDeleteAction extends ActionRuleAction
{
    public static function rules(): array
    {
        return [];
    }

    protected function gate(array $attributes): void
    {
        // condition
    }

    protected function apply(): void
    {
        $this->target->delete();
    }
}
