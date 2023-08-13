<?php

namespace Tests\Feature\Action\Engine\Payment;

use App\Actions\Engine\Payment\PaymentCreateAction;
use App\Actions\Engine\Payment\PaymentUpdateAction;
use App\Actions\Integration\PaymentProvider\NeuronPayment\NeuronPaymentCreateAction;
use App\Enums\Payment\PaymentMethodEnum;
use App\Enums\TriggerEnum;
use App\Facades\SalesChannel;
use App\Models\Engine\Payment;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Exception
     */
    public function test_update_payment(): void
    {
        $merchant = $this->actionMerchantCreate();
        $salesChannel = $this->actionSalesChannelCreate($merchant->id);
        SalesChannel::set($salesChannel);

        $neuronPayment = $this->actionNeuronPaymentCreate($salesChannel->id);
        $payment = $this->actionPaymentCreate($salesChannel->id, $neuronPayment->id, get_class($neuronPayment), [
            'name' => 'Creditcard'
        ]);

        $updatedPayment = $this->actionPaymentUpdate($payment, [
            'name' => 'Updated Creditcard',
            'position' => 2,
        ]);

        $this->assertTrue($updatedPayment->exists);
        $this->assertEquals('Updated Creditcard', $updatedPayment->name);
        $this->assertEquals(2, $updatedPayment->position);
        $this->assertDatabaseCount('payments', 1);
    }
}
