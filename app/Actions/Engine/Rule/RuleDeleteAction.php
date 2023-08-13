<?php

namespace App\Actions\Engine\Rule;

class RuleDeleteAction extends RuleAction
{
    public static function rules(): array
    {
        return [];
    }

    protected function gate(array $attributes): void
    {
        // @todo condition reference
    }

    protected function apply(): void
    {
        $this->target->delete();
    }
}
