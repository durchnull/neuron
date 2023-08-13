<?php

namespace Tests\Feature\Api\Engine\Condition;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Exception
     */
    public function test_condition_delete(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];

        $conditionCreateResponse = $this->apiConditionCreate($salesChannelToken,);

        $conditionDeleteResponse = $this->deleteApiConditionDelete(
            $salesChannelToken,
            $conditionCreateResponse->json()['data']['id']
        );

        $conditionDeleteResponse->assertStatus(200);
    }

    /**
     * @throws Exception
     */
    public function test_deletion_of_referenced_condition(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];

        $conditionCreateResponse = $this->apiConditionCreate($salesChannelToken,);

        $ruleCreateResponse = $this->apiRuleCreate($salesChannelToken, $conditionCreateResponse->json()['data']['id']);

        $conditionDeleteResponse = $this->deleteApiConditionDelete(
            $salesChannelToken,
            $conditionCreateResponse->json()['data']['id']
        );

        // @todo [response] refactor status code
        $conditionDeleteResponse->assertStatus(429);
    }
}
