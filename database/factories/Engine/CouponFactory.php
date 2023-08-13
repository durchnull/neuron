<?php

namespace Database\Factories\Engine;

use App\Facades\Coupon;
use App\Models\Engine\Rule;
use App\Models\Engine\SalesChannel;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Engine\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * @return array<string, mixed>
     * @throws Exception
     */
    public function definition(): array
    {
        return [
            'sales_channel_id' => SalesChannel::factory(),
            'rule_id' => Rule::factory(),
            'name' => $this->faker->sentence,
            'code' => Coupon::generateCode(),
            'enabled' => $this->faker->boolean,
            'combinable' => $this->faker->boolean,
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
