<?php

namespace Database\Seeders;

use App\Models\Engine\SalesChannel;
use App\Models\Engine\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        Transaction::factory()
            ->state([
                'sales_channel_id' => SalesChannel::first()->id
            ])
            ->count(10)
            ->create();
    }
}
