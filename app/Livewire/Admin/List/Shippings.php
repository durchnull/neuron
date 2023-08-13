<?php

namespace App\Livewire\Admin\List;

use App\Models\Engine\Shipping;
use Illuminate\Database\Eloquent\Builder;

class Shippings extends View
{
    public array $search = [
        'name',
        'country_code'
    ];

    public array $tableAttributes = [
        'enabled' => 'status',
        'name' => 'string',
        'country_code' => 'string',
        'net_price' => 'money',
        'gross_price' => 'money',
        'currency_code' => 'string',
        'position' => 'string',
    ];

    public function getBuilder(): Builder
    {
        return Shipping::query();
    }
}
