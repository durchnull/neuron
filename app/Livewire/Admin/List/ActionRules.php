<?php

namespace App\Livewire\Admin\List;

use App\Models\Engine\ActionRule;
use Illuminate\Database\Eloquent\Builder;

class ActionRules extends View
{
    public array $search = [
        'name'
    ];

    public array $tableAttributes = [
        'enabled' => 'status',
        'name' => 'string',
        'action' => 'trans_action',
        'condition' => 'condition',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getBuilder(): Builder
    {
        return ActionRule::with(['condition']);
    }
}
