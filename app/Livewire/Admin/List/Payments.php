<?php

namespace App\Livewire\Admin\List;

use App\Models\Engine\Payment;
use Illuminate\Database\Eloquent\Builder;

class Payments extends View
{
    public array $search = [
        'name',
        'method'
    ];

    public array $tableAttributes = [
        'enabled' => 'status',
        'integration' => 'integration',
        'method' => 'enum',
        'name' => 'string',
        'description' => 'string',
        'position' => 'string',
    ];

    public function getBuilder(): Builder
    {
        return Payment::with('integration');
    }
}
