<?php

namespace Database\Seeders;

use App\Models\Engine\Item;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        Item::factory(10)
            ->create();
    }
}
