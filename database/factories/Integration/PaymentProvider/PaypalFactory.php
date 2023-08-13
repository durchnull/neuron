<?php

namespace Database\Factories\Integration\PaymentProvider;

use App\Models\Engine\SalesChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Integration\PaymentProvider\Paypal>
 */
class PaypalFactory extends Factory
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
            'name' => 'PayPal',
            'client_id' => $this->faker->password(81, 81),
            'client_secret' => $this->faker->password(81, 81),
            'access_token' => $this->faker->password(98, 98),
            'access_token_expires_at' => now()->addMinutes(5),
        ];
    }
}
