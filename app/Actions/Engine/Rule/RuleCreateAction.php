<?php

namespace App\Actions\Engine\Rule;

use App\Consequence\ConsequenceCollection;
use Exception;

class RuleCreateAction extends RuleAction
{

    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid|exists:sales_channels,id',
            'condition_id' => 'required|uuid|exists:conditions,id',
            'name' => 'required|string',
            'consequences' => 'required|array',
            'position' => 'required|integer|min:0',
        ];
    }

    /**
     * @throws Exception
     */
    protected function apply(): void
    {
        $this->validated['consequences'] = ConsequenceCollection::fromArray($this->validated['consequences']);

        $this->target->fill($this->validated)->save();
    }
}
