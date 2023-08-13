<?php

namespace Database\Seeders;

use App\Models\Integration\PaymentProvider\Mollie;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Seeder;

class MollieSeeder extends Seeder
{
    public function run(): void
    {
        Mollie::factory()
            ->state([
                'sales_channel_id' => SalesChannel::first()->id
            ])
            ->count(10)
            ->create();
    }
}
