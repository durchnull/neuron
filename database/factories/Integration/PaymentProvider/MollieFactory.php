<?php

namespace Database\Factories\Integration\PaymentProvider;

use App\Models\Engine\Merchant;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Integration\PaymentProvider\Mollie>
 */
class MollieFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sales_channel_id' => SalesChannel::factory(),
            'enabled' => $this->faker->boolean,
            'name' => 'Mollie',
            'api_key' => $this->faker->lexify('test_??????????????????????????????'),
            'profile_id' => $this->faker->lexify('pfl_??????????'),
        ];
    }
}
