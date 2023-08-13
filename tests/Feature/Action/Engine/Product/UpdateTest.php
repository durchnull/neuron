<?php

namespace Tests\Feature\Action\Engine\Product;

use App\Enums\Product\ProductTypeEnum;
use App\Exceptions\Order\PolicyException;
use App\Facades\SalesChannel;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function test_update_product(): void
    {
        $merchant = $this->actionMerchantCreate();
        $salesChannel = $this->actionSalesChannelCreate($merchant->id);
        SalesChannel::set($salesChannel);

        $inventory = $this->actionNeuronInventoryCreate($salesChannel->id);
        $product = $this->actionProductCreate($salesChannel->id, $inventory->id, get_class($inventory), [
            'type' => ProductTypeEnum::Product->value,
            'net_price' => 1000
        ]);

        $this->assertEquals(1000, $product->net_price);

        $product = $this->actionProductUpdate($product, [
            'net_price' => 1200,
        ]);

        $this->assertEquals(2, $product->version);
        $this->assertEquals(1200, $product->net_price);

        // Triggering the action with the same attributes will not change the product and maintain its version
        $product = $this->actionProductUpdate($product, [
            'net_price' => 1200,
        ]);

        $this->assertEquals(2, $product->version);
        $this->assertEquals(1200, $product->net_price);
    }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function test_update_bundle(): void
    {
        $bundleAndProducts = $this->createProductsAndBundle();

        $configuration = [
            [
                $bundleAndProducts['products'][0]->id,
            ],
            [
                $bundleAndProducts['products'][0]->id,
                $bundleAndProducts['products'][1]->id,
            ],
            [
                $bundleAndProducts['products'][0]->id,
                $bundleAndProducts['products'][1]->id,
                $bundleAndProducts['products'][2]->id,
            ]
        ];


        $bundle = $this->actionProductUpdate($bundleAndProducts['bundle'], [
            'configuration' => $configuration
        ]);

        $this->assertEquals(json_encode($configuration), json_encode($bundle->configuration));
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    protected function createProductsAndBundle(): array
    {
        $merchant = $this->actionMerchantCreate();
        $salesChannel = $this->actionSalesChannelCreate($merchant->id);
        SalesChannel::set($salesChannel);

        $inventory = $this->actionNeuronInventoryCreate($salesChannel->id);
        $product1 = $this->actionProductCreate($salesChannel->id, $inventory->id, get_class($inventory), [
            'type' => ProductTypeEnum::Product->value,
            'inventory_id' => '100001',
            'sku' => 'product-a',
            'name' => 'Product A',
            'net_price' => 1100,
        ]);

        $product2 = $this->actionProductCreate($salesChannel->id, $inventory->id, get_class($inventory), [
            'type' => ProductTypeEnum::Product->value,
            'inventory_id' => '100002',
            'sku' => 'product-b',
            'name' => 'Product B',
            'net_price' => 1200,
        ]);

        $product3 = $this->actionProductCreate($salesChannel->id, $inventory->id, get_class($inventory), [
            'type' => ProductTypeEnum::Product->value,
            'inventory_id' => '100003',
            'sku' => 'product-c',
            'name' => 'Product C',
            'net_price' => 1300,
        ]);

        $productBundle = $this->actionProductCreate($salesChannel->id, $inventory->id, get_class($inventory), [
            'type' => ProductTypeEnum::Product->value,
            'inventory_id' => '100004',
            'sku' => 'product-bundle',
            'name' => 'Product Bundle',
            'net_price' => 10000,
        ]);

        return [
            'bundle' => $productBundle,
            'products' => [
                $product1,
                $product2,
                $product3,
            ],
        ];
    }
}
