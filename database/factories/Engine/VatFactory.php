<?php

namespace Database\Factories\Engine;

use App\Models\Engine\Product;
use App\Models\Engine\SalesChannel;
use App\Models\Engine\Shipping;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Engine\Vat>
 */
class VatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $vatable = $this->faker->randomElement([
            Product::class,
            Shipping::class,
        ]);

        return [
            'sales_channel_id' => SalesChannel::factory(),
            'vatable_id' => $vatable::factory(),
            'vatable_type' => $vatable,
            'country_code' => $this->faker->randomElement([
                'DE',
                'CH',
            ]),
            'rate' => $this->faker->randomElement([
                0,
                700,
                1900,
                2100
            ]),
        ];
    }
}
