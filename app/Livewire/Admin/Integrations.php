<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class Integrations extends Component
{
    public array $tableAttributes = [
        'enabled' => 'status',
        'integration_provider' => 'integration_provider',
        'name' => 'string',
    ];

    public function render()
    {
        $integrations = \App\Facades\Integrations::getModels([]);

        return view('livewire.admin.integration', [
            'models' => $integrations,
            'headline' => class_basename($this),
            'resourceRoute' => 'admin'
        ]);
    }
}
