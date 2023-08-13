<?php

namespace App\Livewire\Admin\List;

use App\Models\Engine\CartRule;
use Illuminate\Database\Eloquent\Builder;

class CartRules extends View
{
    public array $search = [
        'name'
    ];

    public array $tableAttributes = [
        'enabled' => 'status',
        'name' => 'string',
        'rule' => 'rule',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getBuilder(): Builder
    {
        return CartRule::with(['rule']);
    }
}
