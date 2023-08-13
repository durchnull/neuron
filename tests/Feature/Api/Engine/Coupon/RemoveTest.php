<?php

namespace Tests\Feature\Api\Engine\Coupon;

use App\Enums\Payment\PaymentMethodEnum;
use App\Models\Integration\Inventory\NeuronInventory;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class RemoveTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Exception
     */
    public function test_coupon_removal(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $conditionCreateResponse = $this->apiConditionCreate($salesChannelToken, );

        $ruleCreateResponse = $this->apiRuleCreate($salesChannelToken, $conditionCreateResponse->json()['data']['id'], [
                'enabled' => true
            ]);

        $couponCreateResponse = $this->apiCouponCreate($salesChannelToken, $ruleCreateResponse->json()['data']['id'], [
                'name' => $ruleCreateResponse->json()['data']['name'],
                'code' => '10PERCENT',
                'enabled' => true,
            ]);

        $this->assertDatabaseCount('coupons', 1);

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken,);

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

        $couponRedemptionResponse = $this->apiCartCouponRedeem($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                $couponCreateResponse->json()['data']['code']
            );

        $couponRedemptionResponse->assertStatus(200);
        $this->assertCount(1, $couponRedemptionResponse->json()['data']['coupons']);
        $this->assertEquals('10PERCENT', $couponRedemptionResponse->json()['data']['coupons'][0]['code']);

        $couponRemoveResponse = $this->apiCartCouponRemove($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                $couponCreateResponse->json()['data']['code']
            );

        $couponRemoveResponse->assertStatus(200);
        $this->assertCount(0, $couponRemoveResponse->json()['data']['coupons']);
    }

    /**
     * @throws Exception
     */
    public function test_coupon_10_percent_on_cart_and_remove(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $conditionCreateResponse = $this->apiConditionCreate($salesChannelToken, );

        $ruleCreateResponse = $this->apiRuleCreate($salesChannelToken, $conditionCreateResponse->json()['data']['id'], [
                'enabled' => true
            ]);

        $couponCreateResponse = $this->apiCouponCreate($salesChannelToken, $ruleCreateResponse->json()['data']['id'], [
                'name' => $ruleCreateResponse->json()['data']['name'],
                'code' => '10PERCENT',
                'enabled' => true,
            ]);

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken, [
                'net_price' => 300
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

        $neuronInventoryCreateResponse = $this->apiNeuronInventoryCreate($salesChannelToken);

        $productCreateResponse = $this->apiProductCreate($salesChannelToken, [
                'net_price' => 1000,
                'inventoryable_type' => NeuronInventory::class,
                'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
            ]);

        $cartAddResponse = $this->apiCartItemAdd($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                $productCreateResponse->json()['data']['id'],
                2
            );

        $this->assertOrderTotals(
            $cartAddResponse->json()['data'],
            2300,
            2000,
            0,
            300,
            0
        );
        $this->assertCount(1, $cartAddResponse->json()['data']['items']);
        $this->assertEquals(2, $cartAddResponse->json()['data']['items'][0]['quantity']);
        $this->assertEquals(2000, $cartAddResponse->json()['data']['items'][0]['total_amount']);
        $this->assertEquals(1000, $cartAddResponse->json()['data']['items'][0]['unit_amount']);
        $this->assertEquals(0, $cartAddResponse->json()['data']['items'][0]['discount_amount']);

        $couponRedemptionResponse = $this->apiCartCouponRedeem($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                $couponCreateResponse->json()['data']['code']
            );

        $couponRedemptionResponse->assertStatus(200);
        $this->assertCount(1, $couponRedemptionResponse->json()['data']['coupons']);
        $this->assertEquals('10PERCENT', $couponRedemptionResponse->json()['data']['coupons'][0]['code']);
        $this->assertDatabaseCount('coupons', 1);

        $this->assertOrderTotals(
            $couponRedemptionResponse->json()['data'],
            2100,
            2000,
            200,
            300,
            0
        );
        $this->assertCount(1, $couponRedemptionResponse->json()['data']['items']);
        $this->assertEquals(2, $couponRedemptionResponse->json()['data']['items'][0]['quantity']);
        $this->assertEquals(2000, $couponRedemptionResponse->json()['data']['items'][0]['total_amount']);
        $this->assertEquals(1000, $couponRedemptionResponse->json()['data']['items'][0]['unit_amount']);
        $this->assertEquals(200, $couponRedemptionResponse->json()['data']['items'][0]['discount_amount']);

        $cartAddResponse2 = $this->apiCartItemAdd($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                $productCreateResponse->json()['data']['id'],
                1
            );

        $this->assertOrderTotals(
            $cartAddResponse2->json()['data'],
            3000,
            3000,
            300,
            300,
            0
        );
        $this->assertCount(1, $cartAddResponse2->json()['data']['items']);
        $this->assertEquals(3, $cartAddResponse2->json()['data']['items'][0]['quantity']);
        $this->assertEquals(3000, $cartAddResponse2->json()['data']['items'][0]['total_amount']);
        $this->assertEquals(1000, $cartAddResponse2->json()['data']['items'][0]['unit_amount']);
        $this->assertEquals(300, $cartAddResponse2->json()['data']['items'][0]['discount_amount']);

        $couponRemoveResponse = $this->apiCartCouponRemove($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                $couponCreateResponse->json()['data']['code']
            );

        $couponRemoveResponse->assertStatus(200);
        $this->assertCount(0, $couponRemoveResponse->json()['data']['coupons']);

        $this->assertOrderTotals(
            $couponRemoveResponse->json()['data'],
            3300,
            3000,
            0,
            300,
            0
        );
        $this->assertCount(1, $couponRemoveResponse->json()['data']['items']);
        $this->assertEquals(3, $couponRemoveResponse->json()['data']['items'][0]['quantity']);
        $this->assertEquals(3000, $couponRemoveResponse->json()['data']['items'][0]['total_amount']);
        $this->assertEquals(1000, $couponRemoveResponse->json()['data']['items'][0]['unit_amount']);
        $this->assertEquals(0, $couponRemoveResponse->json()['data']['items'][0]['discount_amount']);
    }
}
