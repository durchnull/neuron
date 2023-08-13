<?php

namespace Tests\Feature\Api\Engine\Coupon;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Exception
     */
    public function test_coupon_create(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];

        $conditionCreateResponse = $this->apiConditionCreate($salesChannelToken, );

        $ruleCreateResponse = $this->apiRuleCreate($salesChannelToken, $conditionCreateResponse->json()['data']['id'], [
                'enabled' => true
            ]);

        $couponCreateResponse = $this->apiCouponCreate($salesChannelToken,
                $ruleCreateResponse->json()['data']['id'],
                [
                    'name' => $ruleCreateResponse->json()['data']['name'],
                    'code' => '10PERCENT',
                    'enabled' => true,
                    'combinable' => false,
                ]
            );

        $couponCreateResponse->assertStatus(201);
    }
}
