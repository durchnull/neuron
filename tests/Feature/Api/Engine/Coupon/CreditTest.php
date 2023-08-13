<?php

namespace Tests\Feature\Api\Engine\Coupon;

use App\Condition\Presets\OrderValueIsGreaterOrEqualToAmount;
use App\Condition\Presets\Unconditional;
use App\Consequence\Credit;
use App\Consequence\Discount;
use App\Consequence\Presets\CreditOnAllProducts;
use App\Consequence\Presets\PercentageDiscountOnAllProducts;
use App\Enums\Order\PolicyReasonEnum;
use App\Enums\Payment\PaymentMethodEnum;
use App\Exceptions\Order\PolicyException;
use App\Models\Integration\Inventory\NeuronInventory;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreditTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    public function test_coupon_credit(): void
    {
        $merchant = $this->actionMerchantCreate();
        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);
        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

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

        $couponCreateResponse = $this->createCreditCoupon(
            $salesChannelToken,
            10000,
        );
        $couponRedemptionResponse = $this->apiCartCouponRedeem(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $couponCreateResponse->json()['data']['code']
        );

        $cartPlaceResponse = $this->apiCartPlace($salesChannelCartToken, $cartCreateResponse->json()['data']['id']);

        $this->assertEquals(300, $cartPlaceResponse->json()['data']['amount']);
        $this->assertEquals(1000, $cartPlaceResponse->json()['data']['items_discount_amount']);

        $couponResponse = $this->withToken($salesChannelToken)
            ->postJson('api/coupon', ['id' => $couponCreateResponse->json()['data']['id']]);

        $this->assertEquals(Credit::getType(), $couponResponse->json()['data']['rule']['consequences'][1][0]);
        $this->assertEquals(Discount::getType(), $couponResponse->json()['data']['rule']['consequences'][0][0]);
        $this->assertEquals(9000, $couponResponse->json()['data']['rule']['consequences'][0][1]);
    }

    /**
     * @throws Exception
     */
    protected function createCreditCoupon(
        string $salesChannelToken,
        int $creditAmount,
        array $couponAttributes = []
    ): TestResponse {
        $conditionCreateResponse = $this->apiConditionCreate($salesChannelToken, [
            'name' => Unconditional::name(),
            'collection' => Unconditional::make()->toArray(),
        ]);

        $ruleCreateResponse = $this->apiRuleCreate($salesChannelToken, $conditionCreateResponse->json()['data']['id'], [
            'name' => CreditOnAllProducts::name($creditAmount),
            'consequences' => CreditOnAllProducts::make($creditAmount)->toArray(),
            'enabled' => true
        ]);

        return $this->apiCouponCreate(
            $salesChannelToken,
            $ruleCreateResponse->json()['data']['id'],
            array_merge([
                'name' => $ruleCreateResponse->json()['data']['name'],
                'code' => 'CREDITCOUPON',
                'enabled' => true
            ], $couponAttributes)
        );
    }
}
