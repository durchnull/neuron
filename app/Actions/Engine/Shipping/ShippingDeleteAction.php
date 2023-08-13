<?php

namespace App\Actions\Engine\Shipping;

class ShippingDeleteAction extends ShippingAction
{
    public static function rules(): array
    {
        return [];
    }

    protected function gate(array $attributes): void
    {
        // @todo not referenced in carts
    }

    protected function apply(): void
    {
        $this->target->delete();
    }
}
