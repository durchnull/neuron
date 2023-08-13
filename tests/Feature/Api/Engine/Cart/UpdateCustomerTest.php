<?php

namespace Tests\Feature\Api\Engine\Cart;

use App\Enums\Payment\PaymentMethodEnum;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateCustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_update_customer(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken, [
                'country_code' => 'DE',
            ]);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelCreateResponse->json()['data']['token']);

        $paymentCreateResponse = $this->apiPaymentCreate($salesChannelCreateResponse->json()['data']['token'],
                $neuronPaymentCreateResponse->json()['data']['id'],
                NeuronPayment::class,
                [
                    'name' => Str::ucfirst(PaymentMethodEnum::Creditcard->value),
                    'method' => PaymentMethodEnum::Creditcard->value,
                ]
            );

        $cartCreateResponse = $this->apiCartCreate($salesChannelCartToken,
                $shippingCreateResponse->json()['data']['id'],
                $paymentCreateResponse->json()['data']['id'],
            );

        $updateCustomerResponse = $this->apiCartUpdateCustomer($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                [
                    'email' => 'customer@neuron.de',
                    'shipping_address' => $this->makeAddress([
                        'country_code' => 'DE'
                    ]),
                ]
            );

        $updateCustomerResponse->assertStatus(200);

        $updateCustomerResponse2 = $this->apiCartUpdateCustomer($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                [
                    'email' => 'customer@neuron.de',
                    'shipping_address' => $this->makeAddress([
                        'country_code' => 'DE'
                    ]),
                    'billing_address' => $this->makeAddress([
                        'country_code' => 'CH'
                    ]),
                ]
            );

        $updateCustomerResponse2->assertStatus(200);
    }
}
