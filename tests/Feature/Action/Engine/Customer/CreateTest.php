<?php

namespace Tests\Feature\Action\Engine\Customer;

use App\Exceptions\Order\PolicyException;
use App\Facades\SalesChannel;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function test_create_customer(): void
    {
        $merchant = $this->actionMerchantCreate();
        $salesChannel = $this->actionSalesChannelCreate($merchant->id);
        SalesChannel::set($salesChannel);

        $customer = $this->actionCustomerCreate($salesChannel->id);

        $this->assertTrue($customer->exists);
        $this->assertDatabaseCount('customers', 1);
    }
}
