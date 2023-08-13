<?php

namespace Database\Seeders;

use App\Models\Integration\Inventory\Billbee;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Seeder;

class BillbeeSeeder extends Seeder
{
    public function run(): void
    {
        Billbee::factory()
            ->state([
                'sales_channel_id' => SalesChannel::first()->id
            ])
            ->count(10)
            ->create();
    }
}
