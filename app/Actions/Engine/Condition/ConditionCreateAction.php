<?php

namespace App\Actions\Engine\Condition;

use Exception;

class ConditionCreateAction extends ConditionAction
{

    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid',
            'name' => 'required|string',
            'collection' => 'present|array', // @todo [validation] check present again
        ];
    }

    /**
     * @throws Exception
     */
    protected function apply(): void
    {
        $this->validated['collection'] = \App\Condition\ConditionCollection::fromArray($this->validated['collection']);

        $this->target->fill($this->validated)->save();
    }
}
