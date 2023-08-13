<?php

namespace Database\Seeders;

use App\Models\Integration\Inventory\NeuronInventory;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Seeder;

class NeuronInventorySeeder extends Seeder
{
    public function run(): void
    {
        NeuronInventory::factory()
            ->state([
                'sales_channel_id' => SalesChannel::first()->id
            ])
            ->count(10)
            ->create();
    }
}
