<?php

namespace Tests\Feature\Api\Engine\Rule;

use App\Condition\Presets\Unconditional;
use App\Consequence\Presets\AddFreeItem;
use App\Consequence\Presets\FreeShipping;
use App\Consequence\Presets\PercentageDiscountOnAllProducts;
use App\Consequence\Presets\PercentageDiscountOnProduct;
use App\Enums\Payment\PaymentMethodEnum;
use App\Exceptions\Order\PolicyException;
use App\Models\Integration\Inventory\NeuronInventory;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ConsequenceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Exception
     */
    public function test_rule_consequence_add_free_item(): void
    {
        $setup = $this->getSetup();
        $salesChannelToken = $setup['salesChannelCreateResponse']->json()['data']['token'];
        $salesChannelCartToken = $setup['salesChannelCreateResponse']->json()['data']['cart_token'];

        $cartId = $setup['cartCreateResponse']->json()['data']['id'];

        $conditionCreateResponse = $this->apiConditionCreate($salesChannelToken, [
                'name' => Unconditional::name(),
                'collection' => Unconditional::make()->toArray(),
            ]);

        $ruleCreateResponse = $this->apiRuleCreate($salesChannelToken, $conditionCreateResponse->json()['data']['id'], [
                'name' => AddFreeItem::name(
                    $setup['productCreateResponse']->json()['data']['name'],
                    1,
                    null
                ),
                'consequences' => AddFreeItem::make(
                    $setup['productCreateResponse']->json()['data']['id'],
                    1,
                    null
                )->toArray(),
                'enabled' => true
            ]);

        $couponCreateResponse = $this->apiCouponCreate($salesChannelToken, $ruleCreateResponse->json()['data']['id'], [
                'name' => $ruleCreateResponse->json()['data']['name'],
                'code' => 'FREE' . strtoupper(Str::slug($setup['productCreateResponse']->json()['data']['name'], '')),
                'enabled' => true,
            ]);

        $this->assertOrderTotals(
            $setup['cartCreateResponse']->json()['data'],
            300,
            0,
            0,
            300,
            0
        );

        $couponRedemptionResponse = $this->apiCartCouponRedeem($salesChannelCartToken,
                $cartId,
                $couponCreateResponse->json()['data']['code']
            );

        $this->assertOrderTotals(
            $couponRedemptionResponse->json()['data'],
            300,
            1000,
            1000,
            300,
            0
        );

        $this->assertCount(1, $couponRedemptionResponse->json()['data']['items']);
        $this->assertCount(1, $couponRedemptionResponse->json()['data']['coupons']);
        $this->assertEquals(1000, $couponRedemptionResponse->json()['data']['items'][0]['total_amount']);
        $this->assertEquals(1000, $couponRedemptionResponse->json()['data']['items'][0]['discount_amount']);
    }

    /**
     * @throws Exception
     */
    public function test_rule_consequence_free_shipping(): void
    {
        $setup = $this->getSetup();
        $salesChannelToken = $setup['salesChannelCreateResponse']->json()['data']['token'];
        $salesChannelCartToken = $setup['salesChannelCreateResponse']->json()['data']['cart_token'];
        $cartId = $setup['cartCreateResponse']->json()['data']['id'];

        $paymentCreate2Response = $this->apiPaymentCreate($salesChannelToken,
                $setup['neuronPaymentCreateResponse']->json()['data']['id'],
                NeuronPayment::class,
                [
                    'enabled' => true,
                    'name' => Str::ucfirst(PaymentMethodEnum::Free->value),
                    'method' => PaymentMethodEnum::Free->value,
                    'position' => 2,
                    'description' => 'Free payment',
                ]
            );

        $conditionCreateResponse = $this->apiConditionCreate($salesChannelToken, [
                'name' => Unconditional::name(),
                'collection' => Unconditional::make()->toArray(),
            ]);

        $ruleCreateResponse = $this->apiRuleCreate($salesChannelToken, $conditionCreateResponse->json()['data']['id'], [
                'name' => FreeShipping::name(),
                'consequences' => FreeShipping::make()->toArray(),
                'enabled' => true
            ]);

        $couponCreateResponse = $this->apiCouponCreate($salesChannelToken, $ruleCreateResponse->json()['data']['id'], [
                'name' => $ruleCreateResponse->json()['data']['name'],
                'code' => 'FREESHIPPING',
                'enabled' => true,
            ]);

        $this->assertOrderTotals(
            $setup['cartCreateResponse']->json()['data'],
            300,
            0,
            0,
            300,
            0
        );

        $couponRedemptionResponse = $this->apiCartCouponRedeem($salesChannelCartToken,
                $cartId,
                $couponCreateResponse->json()['data']['code']
            );

        $couponRedemptionResponse->assertStatus(200);

        $this->assertOrderTotals(
            $couponRedemptionResponse->json()['data'],
            0,
            0,
            0,
            300,
            300
        );
    }

    /**
     * @throws Exception
     */
    public function test_rule_consequence_percentage_discount_on_all_products(): void
    {
        $setup = $this->getSetup();
        $salesChannelToken = $setup['salesChannelCreateResponse']->json()['data']['token'];
        $salesChannelCartToken = $setup['salesChannelCreateResponse']->json()['data']['cart_token'];

        $cartId = $setup['cartCreateResponse']->json()['data']['id'];

        $conditionCreateResponse = $this->apiConditionCreate($salesChannelToken, [
                'name' => Unconditional::name(),
                'collection' => Unconditional::make()->toArray(),
            ]);

        $ruleCreateResponse = $this->apiRuleCreate($salesChannelToken, $conditionCreateResponse->json()['data']['id'], [
                'name' => PercentageDiscountOnAllProducts::name('10 %'),
                'consequences' => PercentageDiscountOnAllProducts::make(10)->toArray(),
                'enabled' => true
            ]);

        $couponCreateResponse = $this->apiCouponCreate($salesChannelToken, $ruleCreateResponse->json()['data']['id'], [
                'name' => $ruleCreateResponse->json()['data']['name'],
                'code' => '10PERCENT',
                'enabled' => true
            ]);

        $cartAddResponse = $this->apiCartItemAdd($salesChannelCartToken,
                $cartId,
                $setup['productCreateResponse']->json()['data']['id'],
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
                $cartId,
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
                $cartId,
                $setup['productCreateResponse']->json()['data']['id'],
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
    }

    /**
     * @throws Exception
     */
    public function test_rule_consequence_percentage_discount_on_product(): void
    {
        $setup = $this->getSetup();
        $salesChannelToken = $setup['salesChannelCreateResponse']->json()['data']['token'];
        $salesChannelCartToken = $setup['salesChannelCreateResponse']->json()['data']['cart_token'];

        $cartId = $setup['cartCreateResponse']->json()['data']['id'];

        $productCreateResponse2 = $this->apiProductCreate($salesChannelToken, [
                'net_price' => 600,
                'inventoryable_type' => NeuronInventory::class,
                'inventoryable_id' => $setup['neuronInventoryCreateResponse']->json()['data']['id'],
            ]);

        $cartAddResponse = $this->apiCartItemAdd($salesChannelCartToken,
                $cartId,
                $productCreateResponse2->json()['data']['id'],
                1
            );

        $cartAddResponse2 = $this->apiCartItemAdd($salesChannelCartToken,
                $cartId,
                $setup['productCreateResponse']->json()['data']['id'],
                2
            );

        $this->assertOrderTotals(
            $cartAddResponse2->json()['data'],
            2900,
            2600,
            0,
            300,
            0
        );
        $this->assertCount(2, $cartAddResponse2->json()['data']['items']);
        $this->assertEquals(2, $cartAddResponse2->json()['data']['items'][0]['quantity']);
        $this->assertEquals(2000, $cartAddResponse2->json()['data']['items'][0]['total_amount']);
        $this->assertEquals(1000, $cartAddResponse2->json()['data']['items'][0]['unit_amount']);
        $this->assertEquals(0, $cartAddResponse2->json()['data']['items'][0]['discount_amount']);
        $this->assertEquals(1, $cartAddResponse2->json()['data']['items'][1]['quantity']);
        $this->assertEquals(600, $cartAddResponse2->json()['data']['items'][1]['total_amount']);
        $this->assertEquals(600, $cartAddResponse2->json()['data']['items'][1]['unit_amount']);
        $this->assertEquals(0, $cartAddResponse2->json()['data']['items'][1]['discount_amount']);

        $conditionCreateResponse = $this->apiConditionCreate($salesChannelToken, [
                'name' => Unconditional::name(),
                'collection' => Unconditional::make()->toArray(),
            ]);

        $ruleCreateResponse = $this->apiRuleCreate($salesChannelToken, $conditionCreateResponse->json()['data']['id'], [
                'name' => PercentageDiscountOnProduct::name('10 %', $setup['productCreateResponse']->json()['data']['id']),
                'consequences' => PercentageDiscountOnProduct::make(10, $setup['productCreateResponse']->json()['data']['id'])->toArray(),
                'enabled' => true
            ]);

        $couponCreateResponse = $this->apiCouponCreate($salesChannelToken, $ruleCreateResponse->json()['data']['id'], [
                'name' => $ruleCreateResponse->json()['data']['name'],
                'code' => '10PERCENT',
                'enabled' => true
            ]);

        $couponRedemptionResponse = $this->apiCartCouponRedeem($salesChannelCartToken,
                $cartId,
                $couponCreateResponse->json()['data']['code']
            );

        $couponRedemptionResponse->assertStatus(200);

        $this->assertCount(1, $couponRedemptionResponse->json()['data']['coupons']);
        $this->assertEquals('10PERCENT', $couponRedemptionResponse->json()['data']['coupons'][0]['code']);
        $this->assertDatabaseCount('coupons', 1);

        $this->assertOrderTotals(
            $couponRedemptionResponse->json()['data'],
            2700,
            2600,
            200,
            300,
            0
        );
        $this->assertCount(2, $couponRedemptionResponse->json()['data']['items']);
        $this->assertEquals(1, $couponRedemptionResponse->json()['data']['items'][0]['quantity']);
        $this->assertEquals(600, $couponRedemptionResponse->json()['data']['items'][0]['total_amount']);
        $this->assertEquals(600, $couponRedemptionResponse->json()['data']['items'][0]['unit_amount']);
        $this->assertEquals(0, $couponRedemptionResponse->json()['data']['items'][0]['discount_amount']);
        $this->assertEquals(2, $couponRedemptionResponse->json()['data']['items'][1]['quantity']);
        $this->assertEquals(2000, $couponRedemptionResponse->json()['data']['items'][1]['total_amount']);
        $this->assertEquals(1000, $couponRedemptionResponse->json()['data']['items'][1]['unit_amount']);
        $this->assertEquals(200, $couponRedemptionResponse->json()['data']['items'][1]['discount_amount']);

        $cartAddResponse3 = $this->apiCartItemAdd($salesChannelCartToken,
                $cartId,
                $setup['productCreateResponse']->json()['data']['id'],
                1
            );

        $this->assertOrderTotals(
            $cartAddResponse3->json()['data'],
            3600,
            3600,
            300,
            300,
            0
        );
        $this->assertCount(2, $cartAddResponse3->json()['data']['items']);
        $this->assertEquals(3, $cartAddResponse3->json()['data']['items'][0]['quantity']);
        $this->assertEquals(3000, $cartAddResponse3->json()['data']['items'][0]['total_amount']);
        $this->assertEquals(1000, $cartAddResponse3->json()['data']['items'][0]['unit_amount']);
        $this->assertEquals(300, $cartAddResponse3->json()['data']['items'][0]['discount_amount']);
        $this->assertEquals(1, $cartAddResponse3->json()['data']['items'][1]['quantity']);
        $this->assertEquals(600, $cartAddResponse3->json()['data']['items'][1]['total_amount']);
        $this->assertEquals(600, $cartAddResponse3->json()['data']['items'][1]['unit_amount']);
        $this->assertEquals(0, $cartAddResponse3->json()['data']['items'][1]['discount_amount']);
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     */
    protected function getSetup(): array
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $neuronInventoryCreateResponse = $this->apiNeuronInventoryCreate($salesChannelToken);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelToken);

        $paymentCreateResponse = $this->apiPaymentCreate($salesChannelToken,
                $neuronPaymentCreateResponse->json()['data']['id'],
                NeuronPayment::class,
                [
                    'name' => Str::ucfirst(PaymentMethodEnum::Creditcard->value),
                    'method' => PaymentMethodEnum::Creditcard->value,
                ]
            );

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken, [
                'net_price' => 300
            ]);

        $productCreateResponse = $this->apiProductCreate($salesChannelToken, [
                'net_price' => 1000,
                'inventoryable_type' => NeuronInventory::class,
                'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
            ]);

        $cartCreateResponse = $this->apiCartCreate($salesChannelCartToken,
                $shippingCreateResponse->json()['data']['id'],
                $paymentCreateResponse->json()['data']['id'],
            );

        return [
            'salesChannelCreateResponse' => $salesChannelCreateResponse,
            'neuronInventoryCreateResponse' => $neuronInventoryCreateResponse,
            'neuronPaymentCreateResponse' => $neuronPaymentCreateResponse,
            'paymentCreateResponse' => $paymentCreateResponse,
            'shippingCreateResponse' => $shippingCreateResponse,
            'productCreateResponse' => $productCreateResponse,
            'cartCreateResponse' => $cartCreateResponse,
        ];
    }
}
