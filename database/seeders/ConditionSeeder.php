<?php

namespace Database\Seeders;

use App\Models\Engine\Condition;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Seeder;

class ConditionSeeder extends Seeder
{
    public function run(): void
    {
        Condition::factory()
            ->state([
                'sales_channel_id' => SalesChannel::first()->id
            ])
            ->count(10)
            ->create();
    }
}
