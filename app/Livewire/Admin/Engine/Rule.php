<?php

namespace App\Livewire\Admin\Engine;

use Livewire\Component;

class Rule extends Component
{
    public \App\Models\Engine\Rule $rule;

    public function mount(string $id)
    {
        $this->rule = \App\Models\Engine\Rule::find($id);
    }

    public function render()
    {
        return view('livewire.admin.engine.rule');
    }
}
