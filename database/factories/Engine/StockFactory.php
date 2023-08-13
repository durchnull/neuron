<?php

namespace Database\Factories\Engine;

use App\Models\Engine\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Engine\Stock>
 */
class StockFactory extends Factory
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
            'value' => $this->faker->numberBetween(0, 1000),
            'queue' => $this->faker->numberBetween(0, 1000),
        ];
    }
}
