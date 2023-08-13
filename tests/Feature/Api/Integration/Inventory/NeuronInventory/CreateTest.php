<?php

namespace Tests\Feature\Api\Integration\Inventory\NeuronInventory;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_neuron_inventory(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];

        $neuronInventoryCreateResponse = $this->apiNeuronInventoryCreate($salesChannelToken);

        $neuronInventoryCreateResponse->assertStatus(201);
    }
}
