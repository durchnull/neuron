<?php

namespace App\Actions\Integration\Inventory\NeuronInventory;

class NeuronInventoryCreateAction extends NeuronInventoryAction
{

    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid|exists:sales_channels,id',
            'enabled' => 'required|boolean',
            'receive_inventory' => 'required|boolean',
            'distribute_order' => 'required|boolean',
            'name' => 'required|string|min:3',
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
