<?php

namespace App\Actions\Integration\PaymentProvider\NeuronPayment;

// @todo
class NeuronPaymentDeleteAction extends NeuronPaymentAction
{
    public static function rules(): array
    {
        return [];
    }

    protected function gate(array $attributes): void
    {
    }

    protected function apply(): void
    {
        $this->target->delete();
    }
}
