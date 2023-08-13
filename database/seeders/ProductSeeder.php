<?php

namespace Database\Seeders;

use App\Models\Engine\Product;
use App\Models\Engine\SalesChannel;
use App\Models\Engine\Stock;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::factory(10)
            ->state([
                'sales_channel_id' => SalesChannel::first()->id
            ])
            ->has(Stock::factory())
            ->create();
    }
}
