<?php

namespace Tests\Feature\Action\Engine\Order;

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
    public function test_create_order(): void
    {
        $merchant = $this->actionMerchantCreate();
        $salesChannel = $this->actionSalesChannelCreate($merchant->id);
        SalesChannel::set($salesChannel);

        $shipping = $this->actionShippingCreateAction($salesChannel->id);
        $neuronPayment = $this->actionNeuronPaymentCreate($salesChannel->id);
        $payment = $this->actionPaymentCreate($salesChannel->id, $neuronPayment->id, get_class($neuronPayment));
        $order = $this->actionOrderCreate($salesChannel->id, $shipping->id, $payment->id);

        $this->assertTrue($order->exists);
        $this->assertDatabaseCount('orders', 1);
    }
}
