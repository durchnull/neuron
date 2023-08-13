<?php

namespace Database\Factories\Engine;

use App\Models\Engine\SalesChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Engine\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sales_channel_id' => SalesChannel::factory(),
            'email' => $this->faker->email,
            'full_name' => $this->faker->firstName . ' ' . $this->faker->lastName,
            'phone' => $this->faker->boolean ? $this->faker->e164PhoneNumber : null,
            'order_count' => 0,
            'new' => $this->faker->boolean
        ];
    }
}
