<?php

namespace App\Actions\Integration\Inventory\NeuronInventory;

class NeuronInventoryDeleteAction extends NeuronInventoryAction
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
