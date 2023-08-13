<?php

namespace Tests\Feature\Api\Engine\Cart;

use App\Enums\Payment\PaymentMethodEnum;
use App\Exceptions\Order\PolicyException;
use App\Models\Integration\Inventory\NeuronInventory;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws ValidationException
     * @throws PolicyException
     */
    public function test_order_update_shipping(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $shippingCreateResponse1 = $this->apiShippingCreate($salesChannelToken, [
            'country_code' => 'DE',
            'net_price' => 395
        ]);

        $shippingCreateResponse2 = $this->apiShippingCreate($salesChannelToken, [
            'country_code' => 'DE',
            'net_price' => 295
        ]);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelToken);

        $paymentCreateResponse = $this->apiPaymentCreate(
            $salesChannelToken,
            $neuronPaymentCreateResponse->json()['data']['id'],
            NeuronPayment::class,
            [
                'name' => Str::ucfirst(PaymentMethodEnum::Creditcard->value),
                'method' => PaymentMethodEnum::Creditcard->value,
            ]
        );

        $cartCreateResponse = $this->apiCartCreate(
            $salesChannelCartToken,
            $shippingCreateResponse1->json()['data']['id'],
            $paymentCreateResponse->json()['data']['id'],
        );

        $this->assertEquals(1, $cartCreateResponse->json()['data']['version']);

        $cartUpdateResponse = $this->apiCartUpdateShipping(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $shippingCreateResponse2->json()['data']['id']
        );

        $cartUpdateResponse->assertStatus(200);

        $this->assertEquals(2, $cartUpdateResponse->json()['data']['version']);
        $this->assertCount(0, $cartUpdateResponse->json()['data']['items']);
        $this->assertEquals(295, $cartUpdateResponse->json()['data']['amount']);
        $this->assertEquals(0, $cartUpdateResponse->json()['data']['items_amount']);
        $this->assertEquals(0, $cartUpdateResponse->json()['data']['items_discount_amount']);
        $this->assertEquals(295, $cartUpdateResponse->json()['data']['shipping_amount']);
        $this->assertEquals(0, $cartUpdateResponse->json()['data']['shipping_discount_amount']);
    }

    public function test_order_can_not_update_disabled_shipping(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $shippingCreateResponse1 = $this->apiShippingCreate($salesChannelToken, [
            'country_code' => 'DE',
            'net_price' => 395
        ]);

        $shippingCreateResponse2 = $this->apiShippingCreate($salesChannelToken, [
            'country_code' => 'DE',
            'net_price' => 295,
            'enabled' => false
        ]);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelToken);

        $paymentCreateResponse = $this->apiPaymentCreate(
            $salesChannelToken,
            $neuronPaymentCreateResponse->json()['data']['id'],
            NeuronPayment::class,
            [
                'name' => Str::ucfirst(PaymentMethodEnum::Creditcard->value),
                'method' => PaymentMethodEnum::Creditcard->value,
            ]
        );

        $cartCreateResponse = $this->apiCartCreate(
            $salesChannelCartToken,
            $shippingCreateResponse1->json()['data']['id'],
            $paymentCreateResponse->json()['data']['id']
        );

        $this->assertEquals(1, $cartCreateResponse->json()['data']['version']);

        $cartUpdateResponse = $this->apiCartUpdateShipping(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $shippingCreateResponse2->json()['data']['id']
        );

        $cartUpdateResponse->assertStatus(422);
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     */
    public function test_order_update_payment(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken, [
            'country_code' => 'DE',
            'net_price' => 395
        ]);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelToken);

        $paymentCreateResponse1 = $this->apiPaymentCreate(
            $salesChannelToken,
            $neuronPaymentCreateResponse->json()['data']['id'],
            NeuronPayment::class,
            [
                'name' => Str::ucfirst(PaymentMethodEnum::Creditcard->value),
                'method' => PaymentMethodEnum::Creditcard->value,
            ]
        );

        $paymentCreateResponse2 = $this->apiPaymentCreate(
            $salesChannelToken,
            $neuronPaymentCreateResponse->json()['data']['id'],
            NeuronPayment::class,
            [
                'enabled' => true,
                'name' => Str::ucfirst(PaymentMethodEnum::Prepayment->value),
                'method' => PaymentMethodEnum::Prepayment->value,
                'position' => 2,
                'description' => 'Prepayment',
            ]
        );

        $cartCreateResponse = $this->apiCartCreate(
            $salesChannelCartToken,
            $shippingCreateResponse->json()['data']['id'],
            $paymentCreateResponse1->json()['data']['id']
        );

        $this->assertEquals(1, $cartCreateResponse->json()['data']['version']);

        $cartUpdateResponse = $this->apiCartUpdatePayment(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $paymentCreateResponse2->json()['data']['id']
        );

        $cartUpdateResponse->assertStatus(200);

        $this->assertEquals(2, $cartUpdateResponse->json()['data']['version']);
        $this->assertEquals($paymentCreateResponse2->json()['data']['id'], $cartUpdateResponse->json()['data']['payment']['id']);
    }

    public function test_order_can_not_update_disabled_payment(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken, [
            'country_code' => 'DE',
            'net_price' => 395
        ]);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelToken);

        $paymentCreateResponse1 = $this->apiPaymentCreate(
            $salesChannelToken,
            $neuronPaymentCreateResponse->json()['data']['id'],
            NeuronPayment::class,
            [
                'name' => Str::ucfirst(PaymentMethodEnum::Creditcard->value),
                'method' => PaymentMethodEnum::Creditcard->value,
            ]
        );

        $paymentCreateResponse2 = $this->apiPaymentCreate(
            $salesChannelToken,
            $neuronPaymentCreateResponse->json()['data']['id'],
            NeuronPayment::class,
            [
                'enabled' => false,
                'name' => Str::ucfirst(PaymentMethodEnum::Prepayment->value),
                'method' => PaymentMethodEnum::Prepayment->value,
                'position' => 2,
                'description' => 'Prepayment',
            ]
        );

        $cartCreateResponse = $this->apiCartCreate(
            $salesChannelCartToken,
            $shippingCreateResponse->json()['data']['id'],
            $paymentCreateResponse1->json()['data']['id']
        );

        $this->assertEquals(1, $cartCreateResponse->json()['data']['version']);

        $cartUpdateResponse = $this->apiCartUpdatePayment(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $paymentCreateResponse2->json()['data']['id']
        );

        $cartUpdateResponse->assertStatus(422);
    }

    public function test_can_not_update_order_after_placement()
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken, [
            'country_code' => 'DE',
        ]);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelToken);

        $paymentCreateResponse = $this->apiPaymentCreate(
            $salesChannelToken,
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
            NeuronPayment::class,
            [
                'name' => Str::ucfirst(PaymentMethodEnum::Creditcard->value),
                'method' => PaymentMethodEnum::Creditcard->value,
                'position' => 1,
                'description' => 'Creditcard',
            ]
        );

        $paymentCreateResponse2 = $this->apiPaymentCreate(
            $salesChannelToken,
            $neuronPaymentCreateResponse->json()['data']['id'],
            NeuronPayment::class,
            [
                'name' => Str::ucfirst(PaymentMethodEnum::Creditcard->value),
                'method' => PaymentMethodEnum::Creditcard->value,
                'position' => 2,
                'description' => 'Creditcard',
            ]
        );

        $cartUpdateResponse = $this->apiCartUpdatePayment(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $paymentCreateResponse->json()['data']['id']
        );

        $this->assertEquals(4, $cartUpdateResponse->json()['data']['version']);

        $cartPlaceResponse = $this->apiCartPlace($salesChannelCartToken, $cartCreateResponse->json()['data']['id']);

        $this->assertEquals(6, $cartPlaceResponse->json()['data']['version']);

        $cartUpdateResponse = $this->apiCartUpdatePayment(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $paymentCreateResponse2->json()['data']['id']
        );

        // @todo [response]
        $cartUpdateResponse->assertStatus(409);
    }
}
