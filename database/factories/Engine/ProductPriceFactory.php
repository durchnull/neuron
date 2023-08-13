<?php

namespace Database\Factories\Engine;

use App\Models\Engine\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Engine\ProductPrice>
 */
class ProductPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'net_price' => $this->faker->numberBetween(100, 1000),
            'gross_price' => $this->faker->numberBetween(100, 1000),
            'begin_at' => now(),
            'end_at' => now()->addWeek(),
            'enabled' => $this->faker->boolean,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
