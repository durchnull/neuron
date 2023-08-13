<?php

namespace Tests\Feature\Api\Engine\Rule;

use App\Consequence\Presets\PercentageDiscountOnAllProducts;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Exception
     */
    public function test_rule_create(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];

        $conditionCreateResponse = $this->apiConditionCreate($salesChannelToken, );

        $ruleCreateResponse = $this->apiRuleCreate($salesChannelToken,
                $conditionCreateResponse->json()['data']['id'],
                [
                    'name' => PercentageDiscountOnAllProducts::name(),
                    'consequences' => PercentageDiscountOnAllProducts::make(10)->toArray(),
                    'position' => 0,
                    'enabled' => true,
                ]
            );

        $ruleCreateResponse->assertStatus(201);
        $this->assertEquals(PercentageDiscountOnAllProducts::name(), $ruleCreateResponse->json()['data']['name']);
    }
}
