<?php

namespace Tests\Services\Engine;

use App\Facades\Stock;
use App\Models\Engine\Product;
use App\Models\Engine\SalesChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_has()
    {
        \App\Facades\SalesChannel::set(
            SalesChannel::factory()
                ->state(['use_stock' => true])
                ->create()
        );

        $product = Product::factory()
            ->product()
            ->create();

        $this->assertFalse(Stock::has($product->id));

        Stock::create($product->id, 100);

        $this->assertTrue(Stock::has($product->id));
    }

    public function test_add()
    {
        \App\Facades\SalesChannel::set(
            SalesChannel::factory()
                ->state(['use_stock' => true])
                ->create()
        );

        $product = Product::factory()
            ->product()
            ->create();

        Stock::create($product->id, 100);
        Stock::add($product->id, 100);

        $this->assertEquals(200, Stock::get($product->id));
        $this->assertDatabaseHas('stock', [
            'product_id' => $product->id,
            'value' => 200,
            'queue' => 0
        ]);
    }

    public function test_remove()
    {
        \App\Facades\SalesChannel::set(
            SalesChannel::factory()
                ->state(['use_stock' => true])
                ->create()
        );

        $product = Product::factory()
            ->product()
            ->create();

        Stock::create($product->id, 100);
        Stock::remove($product->id, 50);

        $this->assertEquals(50, Stock::get($product->id));
        $this->assertDatabaseHas('stock', [
            'product_id' => $product->id,
            'value' => 50,
            'queue' => 0
        ]);
    }

    public function test_queue()
    {
        \App\Facades\SalesChannel::set(
            SalesChannel::factory()
                ->state(['use_stock' => true])
                ->create()
        );

        $product = Product::factory()
            ->product()
            ->create();

        Stock::create($product->id, 100);

        $this->assertDatabaseCount('stock', 1);

        Stock::queue($product->id, 10);

        $this->assertDatabaseHas('stock', [
            'product_id' => $product->id,
            'value' => 90,
            'queue' => 10
        ]);

        Stock::queue($product->id, 90);

        $this->assertDatabaseHas('stock', [
            'product_id' => $product->id,
            'value' => 0,
            'queue' => 100
        ]);

        $this->expectException(ValidationException::class);

        Stock::queue($product->id, 10);
    }

    public function test_dequeue()
    {
        \App\Facades\SalesChannel::set(
            SalesChannel::factory()
                ->state(['use_stock' => true])
                ->create()
        );

        $product = Product::factory()
            ->product()
            ->create();

        Stock::create($product->id, 100);
        Stock::queue($product->id, 20);
        Stock::dequeue($product->id, 10);

        $this->assertDatabaseHas('stock', [
            'product_id' => $product->id,
            'value' => 90,
            'queue' => 10
        ]);

        $this->expectException(ValidationException::class);

        Stock::dequeue($product->id, 11);
    }

    public function test_transfer()
    {
        \App\Facades\SalesChannel::set(
            SalesChannel::factory()
                ->state(['use_stock' => true])
                ->create()
        );

        $product = Product::factory()
            ->product()
            ->create();

        Stock::create($product->id, 100);
        Stock::queue($product->id, 20);

        $this->assertDatabaseHas('stock', [
            'product_id' => $product->id,
            'value' => 80,
            'queue' => 20
        ]);

        Stock::transfer($product->id, 10);

        $this->assertDatabaseHas('stock', [
            'product_id' => $product->id,
            'value' => 80,
            'queue' => 10
        ]);

        $this->expectException(ValidationException::class);

        Stock::transfer($product->id, 11);
    }


    public function test_create_stock_for_product()
    {
        \App\Facades\SalesChannel::set(
            SalesChannel::factory()
                ->state(['use_stock' => true])
                ->create()
        );

        $product = Product::factory()
            ->product()
            ->create();

        Stock::create($product->id, 100);

        $this->assertDatabaseCount('stock', 1);
        $this->assertDatabaseHas('stock', [
            'product_id' => $product->id,
            'value' => 100,
            'queue' => 0
        ]);
    }

    public function test_stock_bundle_is_dependant_on_configuration_products_stock()
    {
        \App\Facades\SalesChannel::set(
            SalesChannel::factory()
                ->state(['use_stock' => true])
                ->create()
        );

        $product1 = Product::factory()
            ->product()
            ->create();

        Stock::create($product1->id, 100);

        $this->assertDatabaseCount('stock', 1);
        $this->assertDatabaseHas('stock', [
            'product_id' => $product1->id,
            'value' => 100,
            'queue' => 0
        ]);

        /** @var Product $bundle */
        $bundle = Product::factory()
            ->bundle()
            ->create();

        $this->assertDatabaseCount('stock', 1);

        $configurationProducts = Product::whereIn('id', array_merge(...$bundle->configuration))->get();

        $stocks = array_fill(0, count($configurationProducts), 0);

        foreach ($stocks as $index => $value) {
            $stocks[$index] = $index + 2;
        }

        foreach ($configurationProducts as $index => $configurationProduct) {
            Stock::create($configurationProduct->id, $stocks[$index] + $index);
        }

        $this->assertTrue(Stock::has($bundle->id));
        $this->assertEquals(2, Stock::get($bundle->id));
    }

    public function test_bundle_does_not_create_stock()
    {
        \App\Facades\SalesChannel::set(
            SalesChannel::factory()
                ->state(['use_stock' => true])
                ->create()
        );

        /** @var Product $bundle */
        $bundle = Product::factory()
            ->bundle()
            ->create();

        $this->expectException(ValidationException::class);
        Stock::create($bundle->id, 100);
    }
}
