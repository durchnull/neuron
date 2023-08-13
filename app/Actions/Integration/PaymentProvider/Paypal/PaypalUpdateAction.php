<?php

namespace App\Actions\Integration\PaymentProvider\Paypal;

class PaypalUpdateAction extends PaypalAction
{

    public static function rules(): array
    {
        return [
            'enabled' => 'nullable|boolean',
            'name' => 'nullable|string|min:3',
            'client_id' => 'nullable|string',
            'client_secret' => 'nullable|string',
            'access_token' => 'nullable|string',
            'access_token_expires_at' => 'nullable|date',
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
