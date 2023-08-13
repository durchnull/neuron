<?php

namespace Tests\Feature\Action\Engine\Transaction;

use App\Exceptions\Order\PolicyException;
use App\Facades\SalesChannel;
use App\Services\Integration\PaymentProvider\NeuronPaymentService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
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
    public function test_create_transaction(): void
    {
        $merchant = $this->actionMerchantCreate();
        $salesChannel = $this->actionSalesChannelCreate($merchant->id);
        SalesChannel::set($salesChannel);

        $neuronPayment = $this->actionNeuronPaymentCreate($salesChannel->id);
        $payment = $this->actionPaymentCreate($salesChannel->id, $neuronPayment->id, get_class($neuronPayment));
        $shipping = $this->actionShippingCreateAction($salesChannel->id);
        $order = $this->actionOrderCreate($salesChannel->id, $shipping->id, $payment->id);

        $neuronPaymentService = new NeuronPaymentService($neuronPayment);
        $webhookId = Str::uuid()->toString();
        $resource = $neuronPaymentService->createResource($order, $webhookId);

        $transaction = $this->actionTransactionCreate(
            $salesChannel->id,
            $neuronPayment->id,
            get_class($neuronPayment),
            $order->id,
            [
                'status' => $resource->getStatus(),
                'method' => $resource->getPaymentMethod(),
                'resource_id' => $resource->getId(),
                'resource_data' => $resource->getData(),
                'webhook_id' => $webhookId,
                'checkout_url' => $resource->getCheckoutUrl(),
            ]
        );

        $this->assertTrue($transaction->exists);
        $this->assertDatabaseCount('transactions', 1);
    }
}
