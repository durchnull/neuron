<?php

namespace App\Actions\Integration\PaymentProvider\Paypal;

class PaypalCreateAction extends PaypalAction
{

    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid|exists:sales_channels,id',
            'enabled' => 'required|boolean',
            'name' => 'required|string|min:3',
            'client_id' => 'required|string',
            'client_secret' => 'required|string'
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
