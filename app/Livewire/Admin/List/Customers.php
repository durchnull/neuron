<?php

namespace App\Livewire\Admin\List;

use App\Models\Engine\Customer;
use Illuminate\Database\Eloquent\Builder;

class Customers extends View
{
    public array $search = [
        'email',
        'full_name'
    ];

    public array $tableAttributes = [
        'email' => 'string',
        'full_name' => 'string',
        'phone' => 'string',
    ];

    public function getBuilder(): Builder
    {
        return Customer::query();
    }
}
