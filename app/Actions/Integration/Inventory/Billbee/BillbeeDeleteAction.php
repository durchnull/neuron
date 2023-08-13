<?php

namespace App\Actions\Integration\Inventory\Billbee;

class BillbeeDeleteAction extends BillbeeAction
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
        // @todo move products to other inventory?
        $this->target->delete();
    }
}
