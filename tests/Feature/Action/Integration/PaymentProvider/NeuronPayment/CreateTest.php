<?php

namespace Tests\Feature\Action\Integration\PaymentProvider\NeuronPayment;

use App\Actions\Integration\PaymentProvider\NeuronPayment\NeuronPaymentCreateAction;
use App\Actions\Engine\Merchant\MerchantCreateAction;
use App\Actions\Engine\SalesChannel\SalesChannelCreateAction;
use App\Enums\TriggerEnum;
use App\Models\Engine\SalesChannel;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use App\Models\Engine\Merchant;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Exception
     */
    public function test_create_neuron_payment(): void
    {
        $merchant = $this->actionMerchantCreate();
        $salesChannel = $this->actionSalesChannelCreate($merchant->id);
        \App\Facades\SalesChannel::set($salesChannel);

        $neuronPaymentCreateAction = new NeuronPaymentCreateAction(new NeuronPayment(), [
            'sales_channel_id' => $salesChannel->id,
            'enabled' => true,
            'name' => 'Neuron Payment',
        ], TriggerEnum::App);

        $neuronPaymentCreateAction->trigger();

        /** @var NeuronPayment $neuronPayment */
        $neuronPayment = $neuronPaymentCreateAction->target();

        $this->assertTrue($neuronPayment->exists);
        $this->assertDatabaseCount('integration_neuron_payment', 1);
    }
}
