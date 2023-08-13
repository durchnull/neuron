<?php

namespace Tests\Feature\Api\Engine\Cart;

use App\Enums\Payment\PaymentMethodEnum;
use App\Models\Integration\Inventory\NeuronInventory;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_update_item(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken, [
                'country_code' => 'DE',
            ]);

        $neuronInventoryCreateResponse = $this->apiNeuronInventoryCreate($salesChannelToken);

        $productCreateResponse = $this->apiProductCreate($salesChannelToken, [
                'net_price' => 100,
                'inventoryable_type' => NeuronInventory::class,
                'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
            ]);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelToken);

        $paymentCreateResponse = $this->apiPaymentCreate($salesChannelToken,
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

        $cartAddResponse1 = $this->apiCartItemAdd($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                $productCreateResponse->json()['data']['id'],
                1
            );

        $cartAddResponse2 = $this->apiCartItemAdd($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                $productCreateResponse->json()['data']['id'],
                1
            );

        $cartAddResponse2->assertStatus(200);

        $cart = $cartAddResponse2->json()['data'];

        $this->assertCount(1, $cart['items']);
        $this->assertEquals(2, $cart['items'][0]['quantity']);
        $this->assertEquals(200, $cart['items'][0]['total_amount']);
        $this->assertEquals(100, $cart['items'][0]['unit_amount']);

        $cartUpdateResponse = $this->apiCartItemUpdate($salesChannelCartToken,
                $cart['id'],
                $cart['items'][0]['id'],
                3
            );

        $cartUpdateResponse->assertStatus(200);

        $cart = $cartUpdateResponse->json()['data'];

        $this->assertCount(1, $cart['items']);
        $this->assertEquals(3, $cart['items'][0]['quantity']);
        $this->assertEquals(300, $cart['items'][0]['total_amount']);
        $this->assertEquals(100, $cart['items'][0]['unit_amount']);
    }

    public function test_can_not_update_item_after_placement()
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken, [
                'country_code' => 'DE',
            ]);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelToken);

        $paymentCreateResponse = $this->apiPaymentCreate($salesChannelToken,
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

        $this->assertEquals(1, $cartCreateResponse->json()['data']['version']);

        $neuronInventoryCreateResponse = $this->apiNeuronInventoryCreate($salesChannelToken);

        $productCreateResponse = $this->apiProductCreate($salesChannelToken, [
                'inventoryable_type' => NeuronInventory::class,
                'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
            ]);

        $cartAddResponse = $this->apiCartItemAdd($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                $productCreateResponse->json()['data']['id'],
                1
            );

        $this->assertEquals(2, $cartAddResponse->json()['data']['version']);

        $updateCustomerResponse = $this->apiCartUpdateCustomer($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                [
                    'email' => 'customer@neuron.de',
                    'shipping_address' => $this->makeAddress([
                        'country_code' => 'DE'
                    ]),
                ]
            );

        $this->assertEquals(3, $updateCustomerResponse->json()['data']['version']);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelCreateResponse->json()['data']['token']);

        $paymentCreateResponse = $this->apiPaymentCreate($salesChannelToken,
                $neuronPaymentCreateResponse->json()['data']['id'],
                NeuronPayment::class
            );

        $cartUpdateResponse = $this->apiCartUpdatePayment($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                $paymentCreateResponse->json()['data']['id']
            );

        $this->assertEquals(4, $cartUpdateResponse->json()['data']['version']);

        $cartPlaceResponse = $this->apiCartPlace($salesChannelCartToken, $cartCreateResponse->json()['data']['id']);

        $this->assertEquals(6, $cartPlaceResponse->json()['data']['version']);

        $cartAddResponse2 = $this->apiCartItemAdd($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                $productCreateResponse->json()['data']['id'],
                1
            );

        // @todo [response]
        $cartAddResponse2->assertStatus(409);
    }
}
