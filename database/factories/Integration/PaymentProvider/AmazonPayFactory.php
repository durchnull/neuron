<?php

namespace Database\Factories\Integration\PaymentProvider;

use App\Models\Engine\Merchant;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Integration\PaymentProvider\AmazonPay>
 */
class AmazonPayFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sales_channel_id' => SalesChannel::factory(),
            'merchant_account_id' => $this->faker->uuid,
            'enabled' => $this->faker->boolean,
            'name' => 'Amazon Pay',
            'public_key_id' => $this->faker->lexify('?????'),
            'private_key' => $this->faker->lexify('?????'),
            'region' => $this->faker->lexify('?????'),
            'store_id' => 'amzn1.application-oa2-client.000000000000000000000000000000000',
            'sandbox' => $this->faker->boolean
        ];
    }
}
