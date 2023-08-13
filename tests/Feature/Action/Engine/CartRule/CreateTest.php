<?php

namespace Tests\Feature\Action\Engine\CartRule;

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
    public function test_create_cart_rule(): void
    {
        $merchant = $this->actionMerchantCreate();
        $salesChannel = $this->actionSalesChannelCreate($merchant->id);
        SalesChannel::set($salesChannel);

        $condition = $this->actionConditionCreate($salesChannel->id);
        $rule = $this->actionRuleCreate($salesChannel->id, $condition->id);
        $cartRule = $this->actionCartRuleCreate($salesChannel->id, $rule->id);

        $this->assertTrue($cartRule->exists);
        $this->assertDatabaseCount('cart_rules', 1);
    }

    /*
    public function test_()
    {
        $this->assertDatabaseCount('cart_rule_order', 1);
    }
    */
}
