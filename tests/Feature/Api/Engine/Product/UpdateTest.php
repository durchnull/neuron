<?php

namespace Tests\Feature\Api\Engine\Product;

use App\Models\Integration\Inventory\NeuronInventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_product(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];

        $neuronInventoryCreateResponse = $this->apiNeuronInventoryCreate($salesChannelToken);

        $productCreateResponse = $this->apiProductCreate($salesChannelToken, [
                'name' => 'Product 1',
                'net_price' => 1000,
                'inventoryable_type' => NeuronInventory::class,
                'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
            ]);

        $productCreateResponse->assertStatus(201);
        $this->assertEquals(1, $productCreateResponse->json()['data']['version']);
        $this->assertEquals(1000, $productCreateResponse->json()['data']['net_price']);
        $this->assertEquals('Product 1', $productCreateResponse->json()['data']['name']);

        $productUpdateResponse = $this->apiProductUpdate($salesChannelToken,
                $productCreateResponse->json()['data']['id'],
                [
                    'net_price' => 2000
                ]
            );

        $productUpdateResponse->assertStatus(200);
        $this->assertEquals(2, $productUpdateResponse->json()['data']['version']);
        $this->assertEquals(2000, $productUpdateResponse->json()['data']['net_price']);
        $this->assertEquals('Product 1', $productUpdateResponse->json()['data']['name']);
    }
}
