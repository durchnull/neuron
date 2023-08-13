<?php

namespace App\Actions\Engine\Product;

class ProductDeleteAction extends ProductAction
{
    public static function rules(): array
    {
        return [];
    }

    protected function gate(array $attributes): void
    {
        // @todo does not exist in inventory
        // @todo not referenced in orders
    }

    protected function apply(): void
    {
        // @todo delete product prices
        $this->target->delete();
    }
}
