<?php

namespace Tests\Feature\Action\Engine\Product;

use App\Actions\Engine\Product\ProductCreateAction;
use App\Actions\Integration\Inventory\NeuronInventory\NeuronInventoryCreateAction;
use App\Enums\Product\ProductTypeEnum;
use App\Enums\TriggerEnum;
use App\Facades\SalesChannel;
use App\Models\Engine\Product;
use App\Models\Integration\Inventory\NeuronInventory;
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
    public function test_create_product(): void
    {
        $merchant = $this->actionMerchantCreate();
        $salesChannel = $this->actionSalesChannelCreate($merchant->id);
        SalesChannel::set($salesChannel);

        $inventory = $this->actionNeuronInventoryCreate($salesChannel->id);
        $product = $this->actionProductCreate($salesChannel->id, $inventory->id, get_class($inventory));

        $this->assertTrue($product->exists);
        $this->assertEquals(1, $product->version);
    }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function test_create_bundle(): void
    {
        $merchant = $this->actionMerchantCreate();
        $salesChannel = $this->actionSalesChannelCreate($merchant->id);
        SalesChannel::set($salesChannel);

        $inventory = $this->actionNeuronInventoryCreate($salesChannel->id);

        $product1 = $this->actionProductCreate($salesChannel->id, $inventory->id, get_class($inventory), [
            'type' => ProductTypeEnum::Product->value,
            'sku' => 'product-a'
        ]);

        $product2 = $this->actionProductCreate($salesChannel->id, $inventory->id, get_class($inventory), [
            'type' => ProductTypeEnum::Product->value,
            'sku' => 'product-b'
        ]);

        $product3 = $this->actionProductCreate($salesChannel->id, $inventory->id, get_class($inventory), [
            'type' => ProductTypeEnum::Product->value,
            'sku' => 'product-c'
        ]);

        $productBundle = $this->actionProductCreate($salesChannel->id, $inventory->id, get_class($inventory), [
            'type' => ProductTypeEnum::Bundle->value,
            'sku' => 'product-bundle'
        ]);

        $this->assertTrue($productBundle->exists);
        $this->assertEmpty($productBundle->configuration);

        // @todo [test]
    }
}
