<?php

namespace Database\Seeders;

use App\Models\Engine\SalesChannel;
use Illuminate\Database\Seeder;

class SalesChannelSeeder extends Seeder
{
    public function run(): void
    {
        SalesChannel::factory()
            ->count(10)
            ->create();
    }
}
