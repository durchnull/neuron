<?php

namespace Tests\Feature\Api\Engine\Cart;

use App\Enums\Order\OrderStatusEnum;
use App\Enums\Payment\PaymentMethodEnum;
use App\Enums\Product\ProductTypeEnum;
use App\Exceptions\Order\PolicyException;
use App\Models\Integration\Inventory\NeuronInventory;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AddItemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws ValidationException
     * @throws PolicyException
     */
    public function test_order_add_item_increases_item_quantity(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken, [
            'country_code' => 'DE',
        ]);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelCreateResponse->json()['data']['token']);

        $neuronInventoryCreateResponse = $this->apiNeuronInventoryCreate($salesChannelToken);

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
            $paymentCreateResponse->json()['data']['id']
        );

        $productCreateResponse = $this->apiProductCreate($salesChannelToken, [
            'inventoryable_type' => NeuronInventory::class,
            'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
        ]);

        $cartAddResponse1 = $this->apiCartItemAdd(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $productCreateResponse->json()['data']['id'],
            1
        );

        $cartAddResponse1->assertStatus(200);

        $cart = $cartAddResponse1->json()['data'];

        $this->assertEquals($cart['id'], $cartCreateResponse->json()['data']['id']);
        $this->assertEquals(2, $cart['version']);
        $this->assertCount(1, $cart['items']);
        $this->assertEquals(1, $cart['items'][0]['quantity']);

        $cartAddResponse2 = $this->apiCartItemAdd(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $productCreateResponse->json()['data']['id'],
            2
        );

        $cartAddResponse2->assertStatus(200);

        $cart = $cartAddResponse2->json()['data'];

        $this->assertEquals($cart['id'], $cartCreateResponse->json()['data']['id']);
        $this->assertEquals(3, $cart['version']);
        $this->assertCount(1, $cart['items']);
        $this->assertEquals(3, $cart['items'][0]['quantity']);
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     */
    public function test_order_add_item_creates_distinct_item_if_configuration_is_different()
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken, [
            'country_code' => 'DE',
        ]);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelCreateResponse->json()['data']['token']);

        $neuronInventoryCreateResponse = $this->apiNeuronInventoryCreate($salesChannelToken);

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

        $productCreate1Response = $this->apiProductCreate($salesChannelToken, [
            'inventoryable_type' => NeuronInventory::class,
            'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
            'inventory_id' => '100001',
            'type' => ProductTypeEnum::Product->value,
            'sku' => 'product-a',
            'name' => 'Product A',
        ]);

        $productCreate2Response = $this->apiProductCreate($salesChannelToken, [
            'inventoryable_type' => NeuronInventory::class,
            'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
            'inventory_id' => '100002',
            'type' => ProductTypeEnum::Product->value,
            'sku' => 'product-b',
            'name' => 'Product B',
        ]);

        $productCreate3Response = $this->apiProductCreate($salesChannelToken, [
            'inventoryable_type' => NeuronInventory::class,
            'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
            'inventory_id' => '100003',
            'type' => ProductTypeEnum::Bundle->value,
            'sku' => 'product-c',
            'name' => 'Product C',
            'configuration' => [
                [
                    $productCreate1Response->json()['data']['id']
                ],
                [
                    $productCreate1Response->json()['data']['id'],
                    $productCreate2Response->json()['data']['id'],
                ]
            ]
        ]);

        $configuration1 = [
            $productCreate1Response->json()['data']['id'],
            $productCreate2Response->json()['data']['id'],
        ];

        $cartAddResponse1 = $this->apiCartItemAdd(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $productCreate3Response->json()['data']['id'],
            1,
            $configuration1
        );

        $cartAddResponse1->assertStatus(200);
        $cart = $cartAddResponse1->json()['data'];

        $this->assertEquals($cart['id'], $cartCreateResponse->json()['data']['id']);
        $this->assertEquals(2, $cart['version']);
        $this->assertCount(1, $cart['items']);
        $this->assertEquals(1, $cart['items'][0]['quantity']);
        $this->assertIsArray($cart['items'][0]['configuration']);
        $this->assertCount(2, $cart['items'][0]['configuration']);
        $this->assertEquals($configuration1, $cart['items'][0]['configuration']);

        $configuration2 = [
            $productCreate1Response->json()['data']['id'],
            $productCreate1Response->json()['data']['id'],
        ];

        $cartAddResponse2 = $this->apiCartItemAdd(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $productCreate3Response->json()['data']['id'],
            2,
            $configuration2
        );

        $cartAddResponse2->assertStatus(200);
        $cart = $cartAddResponse2->json()['data'];

        $this->assertEquals($cart['id'], $cartCreateResponse->json()['data']['id']);
        $this->assertEquals(3, $cart['version']);
        $this->assertCount(2, $cart['items']);
        $this->assertEquals(2, $cart['items'][0]['quantity']);
        $this->assertEquals(1, $cart['items'][1]['quantity']);
        $this->assertIsArray($cart['items'][0]['configuration']);
        $this->assertCount(2, $cart['items'][0]['configuration']);
        $this->assertEquals($configuration2, $cart['items'][0]['configuration']);
    }

    public function test_order_add_item_with_disabled_product()
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken, [
            'country_code' => 'DE',
        ]);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelCreateResponse->json()['data']['token']);

        $neuronInventoryCreateResponse = $this->apiNeuronInventoryCreate($salesChannelToken);

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

        $productCreateResponse = $this->apiProductCreate($salesChannelToken, [
            'inventoryable_type' => NeuronInventory::class,
            'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
            'enabled' => false
        ]);

        $cartAddResponse = $this->apiCartItemAdd(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $productCreateResponse->json()['data']['id'],
            1
        );

        $cartAddResponse->assertStatus(422);
    }

    public function test_order_add_item_with_disabled_configuration_product()
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken, [
            'country_code' => 'DE',
        ]);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelCreateResponse->json()['data']['token']);

        $neuronInventoryCreateResponse = $this->apiNeuronInventoryCreate($salesChannelToken);

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

        $productCreate1Response = $this->apiProductCreate($salesChannelToken, [
            'inventoryable_type' => NeuronInventory::class,
            'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
            'inventory_id' => '100001',
            'type' => ProductTypeEnum::Product->value,
            'sku' => 'product-a',
            'name' => 'Product A',
        ]);

        $productCreate2Response = $this->apiProductCreate($salesChannelToken, [
            'inventoryable_type' => NeuronInventory::class,
            'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
            'inventory_id' => '100002',
            'type' => ProductTypeEnum::Product->value,
            'sku' => 'product-b',
            'name' => 'Product B',
            'enabled' => false
        ]);

        $productCreate3Response = $this->apiProductCreate($salesChannelToken, [
            'inventoryable_type' => NeuronInventory::class,
            'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
            'inventory_id' => '100003',
            'type' => ProductTypeEnum::Bundle->value,
            'sku' => 'product-c',
            'name' => 'Product C',
            'configuration' => [
                [
                    $productCreate1Response->json()['data']['id']
                ],
                [
                    $productCreate1Response->json()['data']['id'],
                    $productCreate2Response->json()['data']['id'],
                ]
            ]
        ]);

        $cartAddResponse = $this->apiCartItemAdd(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $productCreate3Response->json()['data']['id'],
            1,
            [
                $productCreate1Response->json()['data']['id'],
                $productCreate2Response->json()['data']['id'],
            ]
        );

        $cartAddResponse->assertStatus(422);
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     */
    public function test_increased_product_price_after_order_add(): void
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

        $neuronInventoryCreateResponse = $this->apiNeuronInventoryCreate($salesChannelToken);

        $productCreateResponse = $this->apiProductCreate($salesChannelToken, [
            'net_price' => 1000,
            'inventoryable_type' => NeuronInventory::class,
            'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
        ]);

        $cartAddResponse = $this->apiCartItemAdd(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $productCreateResponse->json()['data']['id'],
            1
        );

        $cartAddResponse->assertStatus(200);
        $this->assertCount(1, $cartAddResponse->json()['data']['items']);
        $this->assertEquals(1, $cartAddResponse->json()['data']['items'][0]['quantity']);
        $this->assertEquals(1000, $cartAddResponse->json()['data']['items'][0]['total_amount']);

        $productUpdateResponse = $this->apiProductUpdate(
            $salesChannelToken,
            $productCreateResponse->json()['data']['id'],
            [
                'net_price' => 2000
            ]
        );

        $productUpdateResponse->assertStatus(200);

        $cartShowResponse = $this->apiCartGet(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id']
        );

        $cartShowResponse->assertStatus(200);
        $this->assertCount(0, $cartShowResponse->json()['data']['items']);
    }

    public function test_reduced_product_price_after_order_add(): void
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

        $neuronInventoryCreateResponse = $this->apiNeuronInventoryCreate($salesChannelToken);

        $productCreateResponse = $this->apiProductCreate($salesChannelToken, [
            'net_price' => 2000,
            'inventoryable_type' => NeuronInventory::class,
            'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
        ]);

        $cartAddResponse = $this->apiCartItemAdd(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $productCreateResponse->json()['data']['id'],
            1
        );

        $cartAddResponse->assertStatus(200);
        $this->assertCount(1, $cartAddResponse->json()['data']['items']);
        $this->assertEquals(1, $cartAddResponse->json()['data']['items'][0]['quantity']);
        $this->assertEquals(2000, $cartAddResponse->json()['data']['items'][0]['total_amount']);

        $productUpdateResponse = $this->apiProductUpdate(
            $salesChannelToken,
            $productCreateResponse->json()['data']['id'],
            [
                'net_price' => 1000
            ]
        );

        $cartShowResponse = $this->apiCartGet(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id']
        );

        $cartShowResponse->assertStatus(200);
        $this->assertCount(1, $cartShowResponse->json()['data']['items']);
        $this->assertEquals(1, $cartShowResponse->json()['data']['items'][0]['quantity']);
        $this->assertEquals(1000, $cartShowResponse->json()['data']['items'][0]['total_amount']);
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     */
    public function test_can_not_add_item_to_order_after_placement(): void
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

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelCreateResponse->json()['data']['token']);

        $paymentCreateResponse = $this->apiPaymentCreate(
            $salesChannelToken,
            $neuronPaymentCreateResponse->json()['data']['id'],
            NeuronPayment::class,
            [
                'name' => Str::ucfirst(PaymentMethodEnum::Creditcard->value),
                'method' => PaymentMethodEnum::Creditcard->value,
            ]
        );

        $cartUpdateResponse = $this->apiCartUpdatePayment(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $paymentCreateResponse->json()['data']['id']
        );

        $cartPlaceResponse = $this->apiCartPlace($salesChannelCartToken, $cartCreateResponse->json()['data']['id']);

        $cartPlaceResponse->assertStatus(200);
        $this->assertEquals(OrderStatusEnum::Accepted->value, $cartPlaceResponse->json()['data']['status']);

        $cartAddResponse = $this->apiCartItemAdd(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $productCreateResponse->json()['data']['id'],
            1
        );

        // @todo [response]
        $cartAddResponse->assertStatus(409);
        // @todo [response] Maybe add this later if the response should contain the cart
        //$this->assertCount(1, $cartAddResponse->json()['data']);
        //$this->assertEquals(PolicyReasonEnum::OrderFlowConstraint->value, $cartAddResponse->json()['data'][0]);
    }
}
