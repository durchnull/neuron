<?php

namespace App\Actions\Engine\Payment;

class PaymentDeleteAction extends PaymentAction
{
    public static function rules(): array
    {
        return [];
    }

    protected function gate(array $attributes): void
    {
        // @todo not referenced in carts

        // orders
    }

    protected function apply(): void
    {
        $this->target->delete();
    }
}
