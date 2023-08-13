<?php

namespace Database\Seeders;

use App\Models\Engine\Order;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        Order::factory(10)
            ->state([
                'sales_channel_id' => SalesChannel::first()->id
            ])
            ->create();
    }
}
