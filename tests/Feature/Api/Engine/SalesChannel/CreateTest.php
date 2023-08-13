<?php

namespace Tests\Feature\Api\Engine\SalesChannel;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Exception
     */
    public function test_sales_channel_create(): void
    {
        $merchant = $this->actionMerchantCreate();
        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelCreateResponse->assertStatus(201);
    }
}
