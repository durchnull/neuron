<?php

namespace Database\Factories\Integration\Inventory;

use App\Models\Engine\Merchant;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Integration\Inventory\Billbee>
 */
class BillbeeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sales_channel_id' => SalesChannel::factory(),
            'enabled' => $this->faker->boolean,
            'receive_inventory' => $this->faker->boolean,
            'distribute_order' => $this->faker->boolean,
            'name' => 'Billbee',
            'user' => $this->faker->email,
            'api_password' => $this->faker->password,
            'api_key' => $this->faker->uuid,
            'shop_id' => $this->faker->numberBetween(10000000, 100000000),
        ];
    }
}
