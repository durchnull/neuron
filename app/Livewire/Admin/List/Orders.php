<?php

namespace App\Livewire\Admin\List;

use App\Enums\Order\OrderStatusEnum;
use App\Models\Engine\Order;
use Illuminate\Database\Eloquent\Builder;

class Orders extends View
{
    public array $search = [
        'order_number',
        'status'
    ];

    public array $tableAttributes = [
        'order_number' => 'string',
        'status' => 'status',
        'transactions' => 'transactions',
        'amount' => 'money',
        'ordered_at' => 'datetime',
        'payment' => 'payment',
        'customer' => 'customer',
    ];

    public function getBuilder(): Builder
    {
        return Order::with(['customer', 'payment', 'transactions'])
            ->whereNotIn('status', [
                OrderStatusEnum::Open,
                OrderStatusEnum::Placing,
                OrderStatusEnum::Canceled,
            ])->orWhere(function ($query) {
                $query->where('status', OrderStatusEnum::Canceled)
                    ->whereNull('ordered_at');
            });
    }
}
