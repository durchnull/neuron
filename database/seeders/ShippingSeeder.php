<?php

namespace Database\Seeders;

use App\Models\Engine\SalesChannel;
use App\Models\Engine\Shipping;
use Illuminate\Database\Seeder;

class ShippingSeeder extends Seeder
{
    public function run(): void
    {
        Shipping::factory(10)
            ->state([
                'sales_channel_id' => SalesChannel::first()->id
            ])
            ->create();
    }
}
