<?php

namespace Database\Seeders;

use App\Models\Engine\SalesChannel;
use App\Models\Engine\Stock;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        Stock::factory()
            ->count(10)
            ->create();
    }
}
