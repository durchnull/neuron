<?php

namespace Database\Factories\Engine;

use App\Models\Engine\SalesChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Engine\Shipping>
 */
class ShippingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sales_channel_id' => SalesChannel::factory(),
            'enabled' => $this->faker->boolean,
            'name' => $this->faker->word,
            'country_code' => $this->faker->countryCode,
            'net_price' => $this->faker->numberBetween(395, 2950),
            'gross_price' => $this->faker->numberBetween(395, 2950),
            'currency_code' => $this->faker->currencyCode,
            'position' => $this->faker->numberBetween(1, 100),
            'default' => $this->faker->boolean
        ];
    }

    public function enabled(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'enabled' => true,
            ];
        });
    }

    public function disabled(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'enabled' => false,
            ];
        });
    }
}
