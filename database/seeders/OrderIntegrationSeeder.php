<?php

namespace Database\Seeders;

use App\Models\Integration\OrderIntegration;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Seeder;

class OrderIntegrationSeeder extends Seeder
{
    public function run(): void
    {
        OrderIntegration::factory()
            ->state([
                'sales_channel_id' => SalesChannel::first()->id
            ])
            ->count(10)
            ->create();
    }
}
