<?php

namespace Database\Seeders;

use App\Models\Engine\Address;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        Address::factory()
            ->create(10);
    }
}
