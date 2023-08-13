<?php

namespace App\Actions\Integration\PaymentProvider\PostFinance;

// @todo
class PostFinanceDeleteAction extends PostFinanceAction
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
