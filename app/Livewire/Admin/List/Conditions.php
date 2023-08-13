<?php

namespace App\Livewire\Admin\List;

use App\Models\Engine\Condition;
use Illuminate\Database\Eloquent\Builder;

class Conditions extends View
{
    public array $search = [
        'name',
    ];

    public array $tableAttributes = [
        'name' => 'string',
        'collection' => 'condition-collection',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getBuilder(): Builder
    {
        return Condition::query();
    }
}
