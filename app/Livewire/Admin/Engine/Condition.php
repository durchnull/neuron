<?php

namespace App\Livewire\Admin\Engine;

use Livewire\Component;

class Condition extends Component
{
    public string $name;

    public array $collection;

    public function mount(string $id)
    {
        /** @var \App\Models\Engine\Condition $condition */
        $condition = \App\Models\Engine\Condition::find($id);

        $this->name = $condition->name;
        $this->collection = $condition->collection->toArray();
    }

    public function render()
    {
        return view('livewire.admin.engine.condition');
    }
}
