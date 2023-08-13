<?php

namespace Tests\Feature\Api\Engine\Cart;

use App\Enums\Order\OrderStatusEnum;
use App\Enums\Order\PolicyReasonEnum;
use App\Enums\Payment\PaymentMethodEnum;
use App\Models\Integration\Inventory\NeuronInventory;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PlaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_place(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken, [
            'country_code' => 'DE',
        ]);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelCreateResponse->json()['data']['token']);

        $paymentCreateResponse = $this->apiPaymentCreate(
            $salesChannelCreateResponse->json()['data']['token'],
            $neuronPaymentCreateResponse->json()['data']['id'],
            NeuronPayment::class,
            [
                'name' => Str::ucfirst(PaymentMethodEnum::Creditcard->value),
                'method' => PaymentMethodEnum::Creditcard->value,
            ]
        );

        $cartCreateResponse = $this->apiCartCreate(
            $salesChannelCartToken,
            $shippingCreateResponse->json()['data']['id'],
            $paymentCreateResponse->json()['data']['id'],
        );

        $this->assertEquals(1, $cartCreateResponse->json()['data']['version']);

        $neuronInventoryCreateResponse = $this->apiNeuronInventoryCreate($salesChannelToken);

        $productCreateResponse = $this->apiProductCreate($salesChannelToken, [
            'inventoryable_type' => NeuronInventory::class,
            'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
        ]);

        $cartAddResponse = $this->apiCartItemAdd(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $productCreateResponse->json()['data']['id'],
            1
        );

        $this->assertEquals(2, $cartAddResponse->json()['data']['version']);

        $updateCustomerResponse = $this->apiCartUpdateCustomer(
            $salesChannelCartToken,
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

        $paymentCreateResponse = $this->apiPaymentCreate(
            $salesChannelToken,
            $neuronPaymentCreateResponse->json()['data']['id'],
            NeuronPayment::class
        );

        $cartUpdateResponse = $this->apiCartUpdatePayment(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $paymentCreateResponse->json()['data']['id']
        );

        $this->assertEquals(4, $cartUpdateResponse->json()['data']['version']);

        $cartPlaceResponse = $this->apiCartPlace($salesChannelCartToken, $cartCreateResponse->json()['data']['id']);

        $cartPlaceResponse->assertStatus(200);
        $this->assertEquals(6, $cartPlaceResponse->json()['data']['version']);
        $this->assertEquals(OrderStatusEnum::Accepted->value, $cartPlaceResponse->json()['data']['status']);
        $this->assertCount(1, $cartPlaceResponse->json()['data']['transactions']);
    }

    public function test_order_can_not_be_placed_without_items()
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken, [
            'country_code' => 'DE',
        ]);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelCreateResponse->json()['data']['token']);

        $paymentCreateResponse = $this->apiPaymentCreate(
            $salesChannelCreateResponse->json()['data']['token'],
            $neuronPaymentCreateResponse->json()['data']['id'],
            NeuronPayment::class,
            [
                'name' => Str::ucfirst(PaymentMethodEnum::Creditcard->value),
                'method' => PaymentMethodEnum::Creditcard->value,
            ]
        );

        $cartCreateResponse = $this->apiCartCreate(
            $salesChannelCartToken,
            $shippingCreateResponse->json()['data']['id'],
            $paymentCreateResponse->json()['data']['id'],
        );

        $this->assertEquals(1, $cartCreateResponse->json()['data']['version']);
        $this->assertEmpty($cartCreateResponse->json()['data']['items']);

        $updateCustomerResponse = $this->apiCartUpdateCustomer(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            [
                'email' => 'customer@neuron.de',
                'shipping_address' => $this->makeAddress([
                    'country_code' => 'DE'
                ]),
            ]
        );

        $this->assertEquals(2, $updateCustomerResponse->json()['data']['version']);

        $cartPlaceResponse = $this->apiCartPlace($salesChannelCartToken, $cartCreateResponse->json()['data']['id']);

        $cartPlaceResponse->assertStatus(429);
        $this->assertEquals(PolicyReasonEnum::CartIsEmpty->value, $cartPlaceResponse->json()['code']);
    }

    public function test_order_can_not_be_placed_without_customer()
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken, [
            'country_code' => 'DE',
        ]);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelCreateResponse->json()['data']['token']);

        $paymentCreateResponse = $this->apiPaymentCreate(
            $salesChannelCreateResponse->json()['data']['token'],
            $neuronPaymentCreateResponse->json()['data']['id'],
            NeuronPayment::class,
            [
                'name' => Str::ucfirst(PaymentMethodEnum::Creditcard->value),
                'method' => PaymentMethodEnum::Creditcard->value,
            ]
        );

        $cartCreateResponse = $this->apiCartCreate(
            $salesChannelCartToken,
            $shippingCreateResponse->json()['data']['id'],
            $paymentCreateResponse->json()['data']['id'],
        );

        $this->assertEquals(1, $cartCreateResponse->json()['data']['version']);

        $neuronInventoryCreateResponse = $this->apiNeuronInventoryCreate($salesChannelToken);

        $productCreateResponse = $this->apiProductCreate($salesChannelToken, [
            'inventoryable_type' => NeuronInventory::class,
            'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
        ]);

        $cartAddResponse = $this->apiCartItemAdd(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $productCreateResponse->json()['data']['id'],
            1
        );

        $this->assertEquals(2, $cartAddResponse->json()['data']['version']);

        $cartPlaceResponse = $this->apiCartPlace($salesChannelCartToken, $cartCreateResponse->json()['data']['id']);

        $cartPlaceResponse->assertStatus(429);
        $this->assertEquals(PolicyReasonEnum::CustomerNotSet->value, $cartPlaceResponse->json()['code']);
    }
}
