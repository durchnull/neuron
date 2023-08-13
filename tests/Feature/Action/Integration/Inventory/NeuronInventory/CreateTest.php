<?php

namespace Tests\Feature\Action\Integration\Inventory\NeuronInventory;

use App\Actions\Integration\Inventory\NeuronInventory\NeuronInventoryCreateAction;
use App\Actions\Engine\Merchant\MerchantCreateAction;
use App\Actions\Engine\SalesChannel\SalesChannelCreateAction;
use App\Enums\TriggerEnum;
use App\Models\Engine\SalesChannel;
use App\Models\Integration\Inventory\NeuronInventory;
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
    public function test_create_neuron_inventory(): void
    {
        $merchant = $this->actionMerchantCreate();
        $salesChannel = $this->actionSalesChannelCreate($merchant->id);
        \App\Facades\SalesChannel::set($salesChannel);

        $neuronInventoryCreateAction = new NeuronInventoryCreateAction(new NeuronInventory(), [
            'sales_channel_id' => $salesChannel->id,
            'enabled' => true,
            'receive_inventory' => true,
            'distribute_order' => true,
            'name' => 'Neuron Inventory',
        ], TriggerEnum::App);

        $neuronInventoryCreateAction->trigger();

        /** @var NeuronInventory $neuronInventory */
        $neuronInventory = $neuronInventoryCreateAction->target();

        $this->assertTrue($neuronInventory->exists);
        $this->assertDatabaseCount('integration_neuron_inventory', 1);
    }
}
