<?php

namespace Tests\Feature\Action\Engine\Rule;

use App\Facades\SalesChannel;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function test_create_rule(): void
    {
        $merchant = $this->actionMerchantCreate();
        $salesChannel = $this->actionSalesChannelCreate($merchant->id);
        SalesChannel::set($salesChannel);

        $condition = $this->actionConditionCreate($salesChannel->id);
        $rule = $this->actionRuleCreate($salesChannel->id, $condition->id);

        $this->assertTrue($rule->exists);
        $this->assertDatabaseCount('rules', 1);
    }
}
