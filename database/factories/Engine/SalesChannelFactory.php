<?php

namespace Database\Factories\Engine;

use App\Generators\TokenGenerator;
use App\Models\Engine\Merchant;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Engine\SalesChannel>
 */
class SalesChannelFactory extends Factory
{
    /**
     * @return array<string, mixed>
     * @throws Exception
     */
    public function definition(): array
    {
        return [
            'merchant_id' => Merchant::factory(),
            'name' => Str::upper('SC-' . $this->faker->lexify('?????')),
            'currency_code' => $this->faker->randomElement([
                'EUR',
                'CHF',
            ]),
            'token' => TokenGenerator::make()->generate(),
            'cart_token' => TokenGenerator::make()->generate(),
            'domains' => [
                'neuron.ddev.site',
                $this->faker->domainName,
            ],
            'locale' => $this->faker->locale,
            'use_stock' => $this->faker->boolean,
            'remove_items_on_price_increase' => $this->faker->boolean,
            'checkout_summary_url' => route('shop.checkout', ['id' => Str::uuid()->toString()]) . '?checkout-summary',
            'order_summary_url' => route('shop.checkout', ['id' => Str::uuid()->toString()]) . '?order-summary'
        ];
    }
}
