<?php

namespace Database\Seeders;

use App\Models\Integration\PaymentProvider\AmazonPay;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Seeder;

class AmazonPaySeeder extends Seeder
{

    public function run(): void
    {
        AmazonPay::factory()
            ->state([
                'sales_channel_id' => SalesChannel::first()->id
            ])
            ->count(10)
            ->create();
    }
}
