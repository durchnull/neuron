<?php

namespace App\Actions\Engine\Customer;

class CustomerUpdateAction extends CustomerAction
{
    public static function rules(): array
    {
        return [
            'email' => 'nullable|email',
            'full_name' => 'nullable|string',
            'phone' => 'nullable', // @todo [validation] E.164
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
