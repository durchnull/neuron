<?php

namespace App\Actions\Integration\PaymentProvider\Paypal;

// @todo
class PaypalDeleteAction extends PaypalAction
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
