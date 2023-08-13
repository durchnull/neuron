<?php

namespace Database\Factories\Integration\Inventory;

use App\Models\Engine\Merchant;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Integration\Inventory\Weclapp>
 */
class WeclappFactory extends Factory
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
            'name' => 'Weclapp',
            'url' => 'https://' . Str::slug($this->faker->company) . '.weclapp.com/webapp/api/v1/',
            'api_token' => $this->faker->uuid,
            'article_category_id' => $this->faker->numberBetween(1, 9),
            'distribution_channel' => 'GROSS1',
            'warehouse_id' => $this->faker->numberBetween(1, 9),
        ];
    }
}
