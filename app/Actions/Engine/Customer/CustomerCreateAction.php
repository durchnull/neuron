<?php

namespace App\Actions\Engine\Customer;

class CustomerCreateAction extends CustomerAction
{
    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid|exists:sales_channels,id',
            'email' => 'required|email',
            'full_name' => 'required|string',
            'phone' => 'nullable', // @todo [validation] E.164
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
