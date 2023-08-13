<?php

namespace Tests\Feature\Action\Engine\SalesChannel;

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
    public function test_create_merchant(): void
    {
        $merchant = $this->actionMerchantCreate();
        $salesChannel = $this->actionSalesChannelCreate($merchant->id);
        \App\Facades\SalesChannel::set($salesChannel);

        $this->assertTrue($salesChannel->exists);
        $this->assertDatabaseCount('sales_channels', 1);
    }
}
