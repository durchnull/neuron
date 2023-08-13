<?php

namespace Tests\Feature\Api\Engine\CartRule;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Exception
     */
    public function test_cart_rule_create(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];

        $conditionCreateResponse = $this->apiConditionCreate($salesChannelToken,);

        $ruleCreateResponse = $this->apiRuleCreate($salesChannelToken, $conditionCreateResponse->json()['data']['id']);

        $cartRuleCreateResponse = $this->apiCartRuleCreate(
            $salesChannelToken,
            $ruleCreateResponse->json()['data']['id']
        );

        $cartRuleCreateResponse->assertStatus(201);
    }
}
