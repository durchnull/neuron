<?php

namespace Database\Seeders;

use App\Models\Integration\PaymentProvider\Paypal;
use Illuminate\Database\Seeder;

class PaypalSeeder extends Seeder
{
    public function run(): void
    {
        Paypal::factory()
            ->count(10)
            ->create();
    }
}
