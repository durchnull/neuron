<?php

namespace Tests\Feature\Action\Engine\Shipping;

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
    public function test_create_shipping(): void
    {
        $merchant = $this->actionMerchantCreate();
        $salesChannel = $this->actionSalesChannelCreate($merchant->id);
        SalesChannel::set($salesChannel);

        $shipping = $this->actionShippingCreateAction($salesChannel->id);

        $this->assertTrue($shipping->exists);
        $this->assertDatabaseCount('shippings', 1);
    }
}
