<?php

namespace App\Actions\Engine\ActionRule;

use Exception;

class ActionRuleCreateAction extends ActionRuleAction
{

    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid|exists:sales_channels,id',
            'condition_id' => 'required|uuid|exists:conditions,id',
            'name' => 'required|string',
            // @todo
            // 'action' => 'required|string|in:' . implode(',', array_map(fn(string $class) => class_basename($class), Rule::allowedActionRuleClasses())),
            'action' => 'required|string',
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
