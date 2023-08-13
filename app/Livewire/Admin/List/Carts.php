<?php

namespace App\Livewire\Admin\List;

use App\Enums\Order\OrderStatusEnum;
use App\Models\Engine\Order;
use Illuminate\Database\Eloquent\Builder;

class Carts extends View
{
    public array $search = [
        'order_number',
        'status'
    ];

    public array $tableAttributes = [
        'status' => 'status',
        'amount' => 'money',
        'customer' => 'customer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getBuilder(): Builder
    {
        return Order::with('customer')
            ->whereIn('status', [
                OrderStatusEnum::Open,
                OrderStatusEnum::Placing,
            ])->orWhere(function ($query) {
                $query->where('status', OrderStatusEnum::Canceled)
                    ->whereNull('ordered_at');
            });
    }
}
