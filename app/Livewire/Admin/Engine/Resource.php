<?php

namespace App\Livewire\Admin\Engine;

use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class Resource extends Component
{
    public string $modelClass;

    public string $modelId;

    public array $modelAttributes;

    public function mount(string $class, string $id)
    {
        /** @var Model $model */
        $model = $class::find($id);
        $this->modelAttributes = $model->getAttributes();
        $this->modelClass = $class;
        $this->modelId = $id;
    }

    public function render()
    {
        return view('livewire.admin.engine.resource');
    }
}
