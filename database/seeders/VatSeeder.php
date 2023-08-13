<?php

namespace Database\Seeders;

use App\Models\Engine\Vat;
use Illuminate\Database\Seeder;

class VatSeeder extends Seeder
{
    public function run(): void
    {
        Vat::factory()
            ->count(10)
            ->create();
    }
}
