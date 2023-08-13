<?php

namespace Database\Seeders;

use App\Models\Engine\Customer;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::factory(10)
            ->state([
                'sales_channel_id' => SalesChannel::first()->id
            ])
            ->create();
    }
}
