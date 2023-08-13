<?php

namespace Tests\Feature\Action\Engine\Payment;

use App\Actions\Engine\Payment\PaymentCreateAction;
use App\Actions\Integration\PaymentProvider\NeuronPayment\NeuronPaymentCreateAction;
use App\Enums\Payment\PaymentMethodEnum;
use App\Enums\TriggerEnum;
use App\Facades\SalesChannel;
use App\Models\Engine\Payment;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function test_create_payment(): void
    {
        $merchant = $this->actionMerchantCreate();
        $salesChannel = $this->actionSalesChannelCreate($merchant->id);
        SalesChannel::set($salesChannel);

        $neuronPayment = $this->actionNeuronPaymentCreate($salesChannel->id);
        $payment = $this->actionPaymentCreate($salesChannel->id, $neuronPayment->id, get_class($neuronPayment));

        $this->assertTrue($payment->exists);
        $this->assertDatabaseCount('payments', 1);
    }
}
