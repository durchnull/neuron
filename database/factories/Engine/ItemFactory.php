<?php

namespace Database\Factories\Engine;

use App\Models\Engine\Order;
use App\Models\Engine\Product;
use App\Models\Engine\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Engine\Item>
 */
class ItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::factory()
            ->has(Stock::factory());

        return [
            'order_id' => Order::factory(),
            'reference' => $this->faker->boolean ? $this->faker->uuid : null,
            'product_id' => $product,
            'product_version' => $product->make()->version,
            'total_amount' => $this->faker->numberBetween(100, 10000),
            'unit_amount' => $this->faker->numberBetween(100, 10000),
            'discount_amount' => $this->faker->numberBetween(100, 10000),
            'quantity' => $this->faker->numberBetween(1, 10),
            'position' => $this->faker->numberBetween(1, 10),
            'configuration' => null,
            'created_at' => now()->subMinutes($this->faker->numberBetween(30, 60)),
            'updated_at' => now()->subMinutes($this->faker->numberBetween(0, 30)),
        ];
    }
}
