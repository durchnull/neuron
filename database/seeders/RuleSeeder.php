<?php

namespace Database\Seeders;

use App\Models\Engine\Rule;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Seeder;

class RuleSeeder extends Seeder
{
    public function run(): void
    {
        Rule::factory()
            ->state([
                'sales_channel_id' => SalesChannel::first()->id
            ])
            ->count(10)
            ->create();
    }
}
