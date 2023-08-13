<?php

namespace Database\Seeders;

use App\Models\Integration\PaymentProvider\PostFinance;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Seeder;

class PostFinanceSeeder extends Seeder
{
    public function run(): void
    {
        PostFinance::factory()
            ->state([
                'sales_channel_id' => SalesChannel::first()->id
            ])
            ->count(10)
            ->create();
    }
}
