<?php

namespace Database\Seeders;

use App\Models\Engine\Payment;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        Payment::factory()
            ->state([
                'sales_channel_id' => SalesChannel::first()->id
            ])
            ->count(10)
            ->create();
    }
}
