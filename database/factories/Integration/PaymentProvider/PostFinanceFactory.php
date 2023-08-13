<?php

namespace Database\Factories\Integration\PaymentProvider;

use App\Models\Engine\Merchant;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Integration\PaymentProvider\PostFinance>
 */
class PostFinanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sales_channel_id' => SalesChannel::factory(),
            'enabled' => $this->faker->boolean,
            'name' => 'PostFinance',
            'space_id' => $this->faker->numberBetween(100, 1000),
            'user_id' =>  $this->faker->numberBetween(100, 1000),
            'secret' => $this->faker->password,
        ];
    }
}
