<?php

namespace Database\Seeders;

use App\Models\Integration\PaymentProvider\NeuronPayment;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Seeder;

class NeuronPaymentSeeder extends Seeder
{
    public function run(): void
    {
        NeuronPayment::factory()
            ->state([
                'sales_channel_id' => SalesChannel::first()->id
            ])
            ->count(10)
            ->create();
    }
}
