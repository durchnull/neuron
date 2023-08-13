<?php

namespace App\Actions\Integration\PaymentProvider\NeuronPayment;

class NeuronPaymentCreateAction extends NeuronPaymentAction
{

    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid|exists:sales_channels,id',
            'enabled' => 'required|boolean',
            'name' => 'required|string|min:3',
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
