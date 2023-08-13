<?php

use App\Enums\Order\OrderStatusEnum;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('order-events:flush', function () {
    DB::table('order_events')
        ->join('orders', 'orders.id', '=', 'order_events.order_id')
        ->whereIn('orders.status', [
            OrderStatusEnum::Confirmed,
            OrderStatusEnum::Shipped,
            OrderStatusEnum::Refunded,
            OrderStatusEnum::Canceled,
        ])
        ->where('order_events.created_at', '<', now()->subMonth())
        ->delete();
})->purpose('Flush old order events');
