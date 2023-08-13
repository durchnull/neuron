<?php

namespace Database\Factories\Integration;

use App\Enums\Integration\IntegrationResourceStatusEnum;
use App\Models\Integration\Inventory\Billbee;
use App\Models\Integration\Mail\Mailgun;
use App\Models\Engine\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Integration\OrderIntegration>
 */
class OrderIntegrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $integration = $this->faker->randomElement([
            Billbee::class,
            Mailgun::class,
            // @todo [factory] more
        ]);

        return [
            'order_id' => Order::factory(),
            'integration_id' => $integration::factory(),
            'integration_type' => $integration,
            'resource_id' => $this->faker->uuid,
            'status' => $this->faker->randomElement(IntegrationResourceStatusEnum::cases())
        ];
    }
}
