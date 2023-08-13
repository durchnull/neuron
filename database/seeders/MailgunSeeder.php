<?php

namespace Database\Seeders;

use App\Models\Integration\Mail\Mailgun;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Seeder;

class MailgunSeeder extends Seeder
{
    public function run(): void
    {
        Mailgun::factory()
            ->state([
                'sales_channel_id' => SalesChannel::first()->id
            ])
            ->count(10)
            ->create();
    }
}
