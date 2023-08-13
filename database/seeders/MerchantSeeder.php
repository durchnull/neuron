<?php

namespace Database\Seeders;

use App\Models\Engine\Merchant;
use Illuminate\Database\Seeder;

class MerchantSeeder extends Seeder
{
    public function run(): void
    {
        Merchant::factory()
            ->count(10)
            ->create();
    }
}
