<?php

namespace Database\Factories\Integration\Mail;

use App\Models\Engine\Merchant;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Integration\Mail\Mailgun>
 */
class MailgunFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sales_channel_id' => SalesChannel::factory(),
            'enabled' => $this->faker->boolean,
            'distribute_order' => $this->faker->boolean,
            'refund_order' => $this->faker->boolean,
            'name' => 'Mailgun',
            'domain' => Str::slug($this->faker->company) . 'Mail' . $this->faker->domainName,
            'endpoint' => 'api.eu.mailgun.net',
            'secret' => $this->faker->lexify('????????????????????????????????-????????-????????'),
            'api_key' => $this->faker->lexify('???????????????'),
            'order_template' => 'order',
            'refund_template' => 'refund',
            'from' => 'shop@neuron.de',
            'order_subject' => 'Order confirmation {order_id}',
            'refund_subject' => 'Refund {order_id}',
            'sandbox_to' => 'david@brainspin.de',
        ];
    }
}
