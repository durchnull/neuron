<?php

namespace App\Actions\Integration\PaymentProvider\AmazonPay;

// @todo
class AmazonPayDeleteAction extends AmazonPayAction
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
