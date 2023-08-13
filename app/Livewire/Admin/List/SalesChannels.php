<?php

namespace App\Livewire\Admin\List;

use App\Models\Engine\SalesChannel;
use Illuminate\Database\Eloquent\Builder;

class SalesChannels extends View
{
    public array $search = [
        'name'
    ];

    public array $tableAttributes = [
        'name' => 'string',
        'currency_code' => 'string',
        'use_stock' => 'status',
        'remove_items_on_price_increase' => 'status',
        'token' => 'password',
        'cart_token' => 'password',
        'domains' => 'array',
    ];

    public function getBuilder(): Builder
    {
        return SalesChannel::query();
    }
}
