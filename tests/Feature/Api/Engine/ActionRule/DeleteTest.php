<?php

namespace Tests\Feature\Api\Engine\ActionRule;

use App\Actions\Engine\Order\OrderAddItemAction;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Exception
     */
    public function test_action_rule_delete(): void
    {
        $merchant = $this->actionMerchantCreate();
        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);
        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $conditionCreateResponse = $this->apiConditionCreate($salesChannelToken);
        $actionRuleCreateResponse = $this->apiActionRuleCreate(
                $salesChannelToken,
                $conditionCreateResponse->json()['data']['id'],
                class_basename(OrderAddItemAction::class)
            );
        $actionRuleDeleteResponse = $this->apiActionRuleDelete(
            $salesChannelToken,
            $actionRuleCreateResponse->json()['data']['id']
        );

        $actionRuleDeleteResponse->assertStatus(200);
    }
}
