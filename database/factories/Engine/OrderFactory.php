<?php

namespace Database\Factories\Engine;

use App\Enums\Order\OrderStatusEnum;
use App\Facades\Order;
use App\Models\Engine\Address;
use App\Models\Engine\Customer;
use App\Models\Engine\Payment;
use App\Models\Engine\SalesChannel;
use App\Models\Engine\Shipping;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Engine\Order>
 */
class OrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sales_channel_id' => SalesChannel::factory(),
            'shipping_id' => Shipping::factory(),
            'payment_id' => Payment::factory(),
            'customer_id' => Customer::factory(),
            'billing_address_id' => Address::factory(),
            'shipping_address_id' => Address::factory(),
            'amount' => $this->faker->numberBetween(0, 1000),
            'items_amount' => $this->faker->numberBetween(0, 1000),
            'items_discount_amount' => $this->faker->numberBetween(0, 1000),
            'shipping_amount' => $this->faker->numberBetween(0, 1000),
            'shipping_discount_amount' => $this->faker->numberBetween(0, 1000),
            'order_number' => Order::generateOrderNumber(),
            'version' => $this->faker->numberBetween(1, 100),
            'status' => $this->faker->randomElement(OrderStatusEnum::cases())->value,
            'customer_note' => $this->faker->sentence,
            'ordered_at' => $this->faker->boolean ? now()->subMinutes($this->faker->numberBetween(30, 60)) : null,
            'created_at' => $this->faker->boolean
                ? $this->faker->dateTime
                : now(),
            'updated_at' => $this->faker->boolean
                ? $this->faker->dateTime
                : now(),
        ];
    }
}
