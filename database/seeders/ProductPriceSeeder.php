<?php

namespace Database\Seeders;

use App\Models\Engine\ProductPrice;
use Illuminate\Database\Seeder;

class ProductPriceSeeder extends Seeder
{
    public function run(): void
    {
        ProductPrice::factory()
            ->count(10)
            ->create();
    }
}
