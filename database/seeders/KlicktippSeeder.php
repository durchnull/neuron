<?php

namespace Database\Seeders;

use App\Models\Integration\Marketing\Klicktipp;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Seeder;

class KlicktippSeeder extends Seeder
{
    public function run(): void
    {
        Klicktipp::factory()
            ->state([
                'sales_channel_id' => SalesChannel::first()->id
            ])
            ->count(10)
            ->create();
    }
}
