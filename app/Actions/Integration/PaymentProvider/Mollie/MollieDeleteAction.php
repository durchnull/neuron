<?php

namespace App\Actions\Integration\PaymentProvider\Mollie;

// @todo
class MollieDeleteAction extends MollieAction
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
