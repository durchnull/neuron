<?php

namespace Database\Factories\Engine;

use App\Enums\Payment\PaymentMethodEnum;
use App\Models\Integration\PaymentProvider\AmazonPay;
use App\Models\Integration\PaymentProvider\Mollie;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Engine\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $method = $this->faker->randomElement(
            array_map(
                fn(PaymentMethodEnum $paymentMethod) => $paymentMethod->value,
                PaymentMethodEnum::cases()
            )
        );

        $integration = $this->faker->randomElement([
            NeuronPayment::class,
            Mollie::class,
            AmazonPay::class,
        ]);

        return [
            'sales_channel_id' => SalesChannel::factory(),
            'enabled' => $this->faker->boolean,
            'name' => ucfirst($method),
            'integration_type' => $integration,
            'integration_id' => $integration::factory(),
            'method' => $method,
            'position' => $this->faker->numberBetween(1, 100),
            'description' => $this->faker->sentence,
            'default' => $this->faker->boolean
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
