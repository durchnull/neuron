<?php

namespace Tests\Feature\Api\Engine\Condition;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Exception
     */
    public function test_condition_create(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];

        $conditionCreateResponse = $this->apiConditionCreate($salesChannelToken, );

        $conditionCreateResponse->assertStatus(201);
    }
}
