<?php

namespace Tests\Feature\Api\Engine\Cart;

use App\Condition\Presets\Unconditional;
use App\Consequence\Presets\FreeShipping;
use App\Consequence\Presets\PercentageDiscountOnProduct;
use App\Enums\Payment\PaymentMethodEnum;
use App\Exceptions\Order\PolicyException;
use App\Models\Integration\Inventory\NeuronInventory;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    public function test_order_updates_to_free_and_default_payment(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken, [
                'country_code' => 'DE',
                'net_price' => 300,
            ]);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelCreateResponse->json()['data']['token']);

        $neuronInventoryCreateResponse = $this->apiNeuronInventoryCreate($salesChannelToken);

        $paymentCreate1Response = $this->apiPaymentCreate($salesChannelCreateResponse->json()['data']['token'],
                $neuronPaymentCreateResponse->json()['data']['id'],
                NeuronPayment::class,
                [
                    'name' => Str::ucfirst(PaymentMethodEnum::Creditcard->value),
                    'method' => PaymentMethodEnum::Creditcard->value,
                ]
            );

        $this->assertTrue($paymentCreate1Response->json()['data']['default']);

        $paymentCreate2Response = $this->apiPaymentCreate($salesChannelCreateResponse->json()['data']['token'],
                $neuronPaymentCreateResponse->json()['data']['id'],
                NeuronPayment::class,
                [
                    'enabled' => true,
                    'name' => Str::ucfirst(PaymentMethodEnum::Free->value),
                    'method' => PaymentMethodEnum::Free->value,
                    'position' => 2,
                    'description' => 'Free payment',
                ]
            );

        // @todo [response] default is null but should be false
        $this->assertNotTrue($paymentCreate2Response->json()['data']['default']);

        $cartCreateResponse = $this->apiCartCreate($salesChannelCartToken,
                $shippingCreateResponse->json()['data']['id'],
                $paymentCreate1Response->json()['data']['id']
            );

        $cart = $cartCreateResponse->json()['data'];

        $this->assertEquals(1, $cart['version']);

        $productCreateResponse = $this->apiProductCreate($salesChannelToken, [
                'inventoryable_type' => NeuronInventory::class,
                'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
                'net_price' => 1000,
            ]);

        $cartAddResponse = $this->apiCartItemAdd($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                $productCreateResponse->json()['data']['id'],
                1
            );

        $cartAddResponse->assertStatus(200);

        $cart = $cartAddResponse->json()['data'];

        $this->assertEquals($cart['id'], $cartCreateResponse->json()['data']['id']);
        $this->assertEquals(2, $cart['version']);
        $this->assertCount(1, $cart['items']);
        $this->assertEquals(1000, $cart['items_amount']);
        $this->assertEquals($paymentCreate1Response->json()['data']['id'], $cart['payment']['id']);

        $conditionCreateResponse = $this->apiConditionCreate($salesChannelToken, [
                'name' => Unconditional::name(),
                'collection' => Unconditional::make()->toArray(),
            ]);

        $ruleCreateResponse = $this->apiRuleCreate($salesChannelToken, $conditionCreateResponse->json()['data']['id'], [
                'name' => PercentageDiscountOnProduct::name('100 %'),
                'consequences' => array_merge(
                    PercentageDiscountOnProduct::make(100, $productCreateResponse->json()['data']['id'])->toArray(),
                    FreeShipping::make()->toArray()
                ),
                'enabled' => true
            ]);

        $couponCreateResponse = $this->apiCouponCreate($salesChannelToken, $ruleCreateResponse->json()['data']['id'], [
                'name' => $ruleCreateResponse->json()['data']['name'],
                'code' => '100PERCENT',
                'enabled' => true
            ]);

        $couponRedemptionResponse = $this->apiCartCouponRedeem($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                $couponCreateResponse->json()['data']['code']
            );

        $cart = $couponRedemptionResponse->json()['data'];

        $this->assertEquals(4, $cart['version']);
        $this->assertEquals(0, $cart['amount']);
        $this->assertEquals(1000, $cart['items_discount_amount']);
        $this->assertEquals(300, $cart['shipping_discount_amount']);
        $this->assertEquals($paymentCreate2Response->json()['data']['id'], $cart['payment']['id']);

        $couponRemoveResponse = $this->apiCartCouponRemove($salesChannelCartToken,
                $couponRedemptionResponse->json()['data']['id'],
                $couponCreateResponse->json()['data']['code']
            );

        $couponRemoveResponse->assertStatus(200);
        $this->assertCount(0, $couponRemoveResponse->json()['data']['coupons']);
        $this->assertEquals(1300, $couponRemoveResponse->json()['data']['amount']);
        $this->assertEquals($paymentCreate1Response->json()['data']['id'], $couponRemoveResponse->json()['data']['payment']['id']);
    }
}
