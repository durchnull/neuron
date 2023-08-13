<?php

namespace App\Livewire\Admin\Integration;

use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;
use Livewire\Component;

class NeuronInventory extends Component
{
    public string $headline;

    #[Locked]
    public string $id;

    #[Rule('required|exists:sales_channels,id')]
    public string $salesChannelId;

    #[Rule('required|min:3')]
    public string $name;

    #[Rule('required|bool')]
    public bool $enabled;

    #[Rule('required|bool')]
    public bool $distributeOrder;

    #[Rule('required|bool')]
    public bool $receiveInventory;

    public \App\Models\Integration\Inventory\NeuronInventory $neuron;

    public function mount(string $id)
    {
        $neuronInventory = \App\Models\Integration\Inventory\NeuronInventory::find($id);

        $this->headline = $neuronInventory->name;

        $this->id = $id;
        $this->salesChannelId = $neuronInventory->sales_channel_id;
        $this->name = $neuronInventory->name;
        $this->enabled = $neuronInventory->enabled;
        $this->distributeOrder = $neuronInventory->distribute_order;
        $this->receiveInventory = $neuronInventory->receive_inventory;
    }

    public function save()
    {
        $this->validate();

        \App\Models\Integration\Inventory\NeuronInventory::where('id', $this->id)->update([
            'sales_channel_id' => $this->salesChannelId,
            'name' => $this->name,
            'enabled' => $this->enabled,
            'distribute_order' => $this->distributeOrder,
            'receive_inventory' => $this->receiveInventory,
        ]);
    }

    public function render()
    {
        return view('livewire.admin.integration.neuron-inventory');
    }
}
