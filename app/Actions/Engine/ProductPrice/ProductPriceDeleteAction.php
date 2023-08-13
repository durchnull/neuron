<?php

namespace App\Actions\Engine\ProductPrice;

class ProductPriceDeleteAction extends ProductPriceAction
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
