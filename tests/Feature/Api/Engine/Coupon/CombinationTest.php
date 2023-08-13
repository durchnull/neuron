<?php

namespace Tests\Feature\Api\Engine\Coupon;

use App\Condition\Presets\OrderValueIsGreaterOrEqualToAmount;
use App\Condition\Presets\Unconditional;
use App\Consequence\Presets\PercentageDiscountOnAllProducts;
use App\Enums\Order\PolicyReasonEnum;
use App\Enums\Payment\PaymentMethodEnum;
use App\Exceptions\Order\PolicyException;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CombinationTest extends TestCase
{
    use RefreshDatabase;


    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    public function test_coupon_combinables(): void
    {
        $merchant = $this->actionMerchantCreate();
        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);
        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $couponCreateResponse1 = $this->createPercentageDiscountCoupon($salesChannelToken, 10);
        $couponCreateResponse2 = $this->createPercentageDiscountCoupon($salesChannelToken, 20);
        $couponCreateResponse3 = $this->createPercentageDiscountCoupon($salesChannelToken, 30, [
            'combinable' => true
        ]);
        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken);
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
        $couponRedemptionResponse = $this->apiCartCouponRedeem(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $couponCreateResponse1->json()['data']['code']
        );

        $couponRedemptionResponse->assertStatus(200);
        $this->assertCount(1, $couponRedemptionResponse->json()['data']['coupons']);
        $this->assertEquals('10PERCENT', $couponRedemptionResponse->json()['data']['coupons'][0]['code']);

        $couponRedemptionResponse = $this->apiCartCouponRedeem(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $couponCreateResponse2->json()['data']['code']
        );

        $couponRedemptionResponse->assertStatus(429);
        $this->assertEquals(PolicyReasonEnum::CouponIsNotCombinable->value, $couponRedemptionResponse->json()['code']);

        $couponRedemptionResponse = $this->apiCartCouponRedeem(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $couponCreateResponse3->json()['data']['code']
        );

        $couponRedemptionResponse->assertStatus(200);
        $this->assertCount(2, $couponRedemptionResponse->json()['data']['coupons']);
        $this->assertEquals('30PERCENT', $couponRedemptionResponse->json()['data']['coupons'][0]['code']);
        $this->assertEquals('10PERCENT', $couponRedemptionResponse->json()['data']['coupons'][1]['code']);
    }

    /**
     * @throws Exception
     */
    protected function createPercentageDiscountCoupon(
        string $salesChannelToken,
        int $percentage,
        array $couponAttributes = []
    ): TestResponse {
        if (!($percentage > 0 && $percentage <= 100)) {
            throw new Exception('Percentage must be between 0 and 100');
        }

        $conditionCreateResponse = $this->apiConditionCreate($salesChannelToken, [
            'name' => Unconditional::name(),
            'collection' => Unconditional::make()->toArray(),
        ]);

        $ruleCreateResponse = $this->apiRuleCreate($salesChannelToken, $conditionCreateResponse->json()['data']['id'], [
            'name' => PercentageDiscountOnAllProducts::name($percentage . ' %'),
            'consequences' => PercentageDiscountOnAllProducts::make($percentage)->toArray(),
            'enabled' => true
        ]);

        return $this->apiCouponCreate(
            $salesChannelToken,
            $ruleCreateResponse->json()['data']['id'],
            array_merge([
                'name' => $ruleCreateResponse->json()['data']['name'],
                'code' => $percentage . 'PERCENT',
                'enabled' => true
            ], $couponAttributes)
        );
    }
}
