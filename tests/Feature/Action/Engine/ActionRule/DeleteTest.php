<?php

namespace Tests\Feature\Action\Engine\ActionRule;

use App\Condition\Presets\MaxActionProductQuantity;
use App\Facades\SalesChannel;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function test_delete_action_rule(): void
    {
        $merchant = $this->actionMerchantCreate();
        $salesChannel = $this->actionSalesChannelCreate($merchant->id);
        SalesChannel::set($salesChannel);

        $condition = $this->actionConditionCreate($salesChannel->id);
        $actionRule = $this->actionActionRuleCreate($salesChannel->id, $condition->id);
        $this->actionActionRuleDelete($actionRule);

        $this->assertFalse($actionRule->exists);
        $this->assertDatabaseCount('action_rules', 0);
    }
}
