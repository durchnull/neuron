<?php

namespace App\Livewire\Admin\List;

use App\Models\Engine\Rule;
use Illuminate\Database\Eloquent\Builder;

class Rules extends View
{
    public array $search = [
        'name',
    ];

    public array $tableAttributes = [
        'name' => 'string',
        'condition' => 'condition',
        'consequences' => 'consequences',
        'enabled' => 'bool',
    ];

    public function getBuilder(): Builder
    {
        return Rule::query();
    }
}
