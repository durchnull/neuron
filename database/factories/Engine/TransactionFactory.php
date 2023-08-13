<?php

namespace Database\Factories\Engine;

use App\Enums\Transaction\TransactionStatusEnum;
use App\Models\Engine\Order;
use App\Models\Engine\SalesChannel;
use App\Models\Integration\PaymentProvider\AmazonPay;
use App\Models\Integration\PaymentProvider\Mollie;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Engine\Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        $integration = $this->faker->randomElement([
            NeuronPayment::class,
            Mollie::class,
            AmazonPay::class,
        ]);

        $integration = $integration::factory()->create();

        $resourceData = null;

        $status = $this->faker->randomElement(TransactionStatusEnum::cases());
        $webhookId = $this->faker->uuid;
        $resourceId = $this->faker->uuid;
        $checkoutUrl = "https://" . Str::slug(class_basename($integration)) . ".de/checkout/$resourceId";

        if (get_class($integration) === Mollie::class) {
            $resourceId = 'ord_' . $this->faker->lexify('??????????');
            $resourceData = [
                'card_token' => 'tkn_' . $this->faker->lexify('??????????'),
            ];
            $checkoutUrl = "https://mollie.de/order/$resourceId";
        } elseif (get_class($integration) === AmazonPay::class) {
            $resourceId = $this->faker->uuid;
            $webhookId = null;
            $checkoutUrl = "https://payments.amazon.de/checkout/processing?amazonCheckoutSessionId=$resourceId";

            if ($status === TransactionStatusEnum::Authorized) {
                $n1 = $this->faker->numberBetween(1000000, 9999999);
                $n2 = $this->faker->numberBetween(1000000, 9999999);
                $n3 = $this->faker->lexify('?') . $this->faker->numberBetween(100000, 999999);

                $resourceData = [
                    'chargePermissionId' => "S02-$n1-$n2",
                    'chargeId' => "S02-$n1-$n2-$n3"
                ];
            }
        }

        return [
            'sales_channel_id' => SalesChannel::factory(),
            'order_id' => Order::factory(),
            'integration_type' => get_class($integration),
            'integration_id' => $integration->id,
            'status' => $status,
            'resource_id' => $resourceId,
            'resource_data' => $resourceData,
            'webhook_id' => $webhookId,
            'checkout_url' => $checkoutUrl,
        ];
    }
}
