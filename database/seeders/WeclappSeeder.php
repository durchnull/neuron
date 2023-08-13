<?php

namespace Database\Seeders;

use App\Models\Integration\Inventory\Weclapp;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Seeder;

class WeclappSeeder extends Seeder
{
    public function run(): void
    {
        Weclapp::factory()
            ->state([
                'sales_channel_id' => SalesChannel::first()->id
            ])
            ->count(10)
            ->create();
    }
}
