<?php

namespace Database\Seeders;

use App\Models\Engine\CartRule;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Seeder;

class CartRuleSeeder extends Seeder
{
    public function run(): void
    {
        CartRule::factory()
            ->state([
                'sales_channel_id' => SalesChannel::first()->id
            ])
            ->count(10)
            ->create();
    }
}
