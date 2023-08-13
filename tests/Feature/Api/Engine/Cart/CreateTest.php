<?php

namespace Tests\Feature\Api\Engine\Cart;

use App\Enums\Order\OrderStatusEnum;
use App\Enums\Payment\PaymentMethodEnum;
use App\Exceptions\Order\PolicyException;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws ValidationException
     * @throws PolicyException
     */
    public function test_order_create(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken, [
                'country_code' => 'DE',
                'net_price' => 395
            ]);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelCreateResponse->json()['data']['token']);

        $paymentCreateResponse = $this->apiPaymentCreate($salesChannelToken,
                $neuronPaymentCreateResponse->json()['data']['id'],
                NeuronPayment::class,
                [
                    'name' => Str::ucfirst(PaymentMethodEnum::Creditcard->value),
                    'method' => PaymentMethodEnum::Creditcard->value,
                ]
            );

        $cartCreateResponse = $this->apiCartCreate($salesChannelCartToken, $shippingCreateResponse->json()['data']['id'], $paymentCreateResponse->json()['data']['id']);

        $cartCreateResponse->assertStatus(201);

        $this->assertEquals(1, $cartCreateResponse->json()['data']['version']);
        $this->assertEquals(OrderStatusEnum::Open->value, $cartCreateResponse->json()['data']['status']);
        $this->assertCount(0, $cartCreateResponse->json()['data']['items']);
        $this->assertOrderTotals(
            $cartCreateResponse->json()['data'],
            395,
            0,
            0,
            395,
            0
        );
    }
}
