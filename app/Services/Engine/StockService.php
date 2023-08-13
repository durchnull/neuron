<?php

namespace App\Services\Engine;

use App\Actions\Engine\Stock\StockCreateAction;
use App\Actions\Engine\Stock\StockUpdateAction;
use App\Contracts\Engine\StockServiceContract;
use App\Enums\Product\ProductTypeEnum;
use App\Enums\TriggerEnum;
use App\Exceptions\Order\PolicyException;
use App\Models\Engine\Order;
use App\Models\Engine\Item;
use App\Models\Engine\Product;
use App\Models\Engine\Stock;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class StockService implements StockServiceContract
{
    /**
     * @throws ValidationException
     * @throws PolicyException
     */
    public function queueOrder(Order $order): StockServiceContract
    {
        /** @var Item $item */
        foreach ($order->items as $item) {
            if ($item->product->type === ProductTypeEnum::Product) {
                $this->queue($item->product_id, $item->quantity);
            } elseif ($item->product->type === ProductTypeEnum::Bundle && is_array($item->configuration)) { // @todo when is configuration null (not cast to array) ?
                foreach ($item->configuration as $configurationProductId) {
                    $this->queue($configurationProductId, 1);
                }
            }
        }

        return $this;
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    public function create(string $productId, int $quantity): Stock
    {
        $stockCreateAction = new StockCreateAction(new Stock(), [
            'product_id' => $productId,
            'value' => $quantity,
        ], TriggerEnum::App);

        $stockCreateAction->trigger();

        return $stockCreateAction->target();
    }

    public function has(string $productId, int $quantity = 1, bool $queue = false): bool
    {
        return $this->get($productId, $queue) >= $quantity;
    }

    public function get(string $productId, bool $queue = false): int
    {
        $product = Product::where('id', $productId)->first();

        if ($product->type === ProductTypeEnum::Bundle) {

            if ($product->configuration === null) {
                return 0;
            }

            return min(
                array_map(
                    fn(array $group) => min(
                        array_map(
                            fn(string $productId) => $this->get($productId),
                            $group
                        )
                    ),
                    $product->configuration
                )
            );
        }

        return Stock::where('product_id', $productId)->value($queue ? 'queue' : 'value') ?? 0;
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    public function add(string $productId, int $quantity): StockServiceContract
    {
        $stock = Stock::where('product_id', $productId)->first();

        if ($stock) {
            $action = new StockUpdateAction($stock, [
                'product_id' => $productId,
                'value' => $stock->value + $quantity,
            ], TriggerEnum::App);

            $action->trigger();
        }

        return $this;
    }


    /**
     * @param  string  $productId
     * @param  int  $quantity
     * @return StockServiceContract
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function remove(string $productId, int $quantity): StockServiceContract
    {
        $stock = Stock::where('product_id', $productId)->first();

        if ($stock) {
            $action = new StockUpdateAction($stock, [
                'product_id' => $productId,
                'value' => $stock->value - $quantity,
            ], TriggerEnum::App);

            $action->trigger();
        }

        return $this;
    }

    /**
     * @param  string  $productId
     * @param  int  $quantity
     * @return StockServiceContract
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function queue(string $productId, int $quantity): StockServiceContract
    {
        $stock = Stock::where('product_id', $productId)->first();

        if ($stock) {
            $action = new StockUpdateAction($stock, [
                'product_id' => $productId,
                'value' => $stock->value - $quantity,
                'queue' => $stock->queue + $quantity,
            ], TriggerEnum::App);

            $action->trigger();
        }

        return $this;
    }

    /**
     * @param  string  $productId
     * @param  int  $quantity
     * @return StockServiceContract
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function dequeue(string $productId, int $quantity): StockServiceContract
    {
        $stock = Stock::where('product_id', $productId)->first();

        if ($stock) {
            $action = new StockUpdateAction($stock, [
                'product_id' => $productId,
                'value' => $stock->value + $quantity,
                'queue' => $stock->queue - $quantity,
            ], TriggerEnum::App);

            $action->trigger();
        }

        return $this;
    }

    /**
     * @param  string  $productId
     * @param  int  $quantity
     * @return StockServiceContract
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function transfer(string $productId, int $quantity): StockServiceContract
    {
        $stock = Stock::where('product_id', $productId)->first();

        if ($stock) {
            $action = new StockUpdateAction($stock, [
                'product_id' => $productId,
                'queue' => $stock->queue - $quantity,
            ], TriggerEnum::App);

            $action->trigger();
        }

        return $this;
    }
}
