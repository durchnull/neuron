<?php

namespace Database\Seeders;

use App\Models\Engine\ActionRule;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Seeder;

class ActionRuleSeeder extends Seeder
{
    public function run(): void
    {
        ActionRule::factory()
            ->state([
                'sales_channel_id' => SalesChannel::first()->id
            ])
            ->count(10)
            ->create();
    }
}
