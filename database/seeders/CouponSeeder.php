<?php

namespace Database\Seeders;

use App\Models\Engine\Coupon;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::factory()
            ->state([
                'sales_channel_id' => SalesChannel::first()->id
            ])
            ->count(10)
            ->create();
    }
}
