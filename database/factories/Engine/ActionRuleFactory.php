<?php

namespace Database\Factories\Engine;

use App\Actions\Engine\Order\OrderAddItemAction;
use App\Actions\Engine\Order\OrderUpdatePaymentAction;
use App\Models\Engine\Condition;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Engine\ActionRule>
 */
class ActionRuleFactory extends Factory
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
            'condition_id' => Condition::factory(),
            'name' => $this->faker->sentence,
            'action' => $this->faker->randomElement([
                class_basename(OrderAddItemAction::class),
                class_basename(OrderUpdatePaymentAction::class),
                // @todo [migration] more
            ]),
            'enabled' => $this->faker->boolean,
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
