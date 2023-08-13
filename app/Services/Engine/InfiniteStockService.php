<?php

namespace App\Services\Engine;

use App\Actions\Engine\Stock\StockCreateAction;
use App\Contracts\Engine\StockServiceContract;
use App\Enums\TriggerEnum;
use App\Exceptions\Order\PolicyException;
use App\Models\Engine\Order;
use App\Models\Engine\Stock;
use Exception;
use Illuminate\Validation\ValidationException;

class InfiniteStockService implements StockServiceContract
{
    public const INFINITE_STOCK = 9999;

    public function queueOrder(Order $order): StockServiceContract
    {
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
            'value' => self::INFINITE_STOCK,
        ], TriggerEnum::App);

        $stockCreateAction->trigger();

        return $stockCreateAction->target();
    }

    public function has(string $productId, int $quantity = 1, bool $queue = false): bool
    {
        return true;
    }

    public function get(string $productId, bool $queue = false): int
    {
        return self::INFINITE_STOCK;
    }

    public function add(string $productId, int $quantity): StockServiceContract
    {
        // Do nothing

        return $this;
    }

    public function remove(string $productId, int $quantity): StockServiceContract
    {
        // Do nothing

        return $this;
    }

    public function queue(string $productId, int $quantity): StockServiceContract
    {
        // Do nothing

        return $this;
    }

    public function dequeue(string $productId, int $quantity): StockServiceContract
    {
        // Do nothing

        return $this;
    }

    public function transfer(string $productId, int $quantity): StockServiceContract
    {
        // Do nothing

        return $this;
    }
}
