<?php

namespace App\Facades;

use App\Contracts\Engine\StockServiceContract;
use App\Services\Engine\StockService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static StockServiceContract queueOrder(\App\Models\Engine\Order $order)
 * @method static \App\Models\Engine\Stock create(string $productId, int $quantity)
 * @method static bool has(string $productId, int $quantity = 1, bool $queue = false)
 * @method static int get(string $productId, bool $queue = false)
 * @method static StockServiceContract add(string $productId, int $quantity)
 * @method static StockServiceContract remove(string $productId, int $quantity)
 * @method static StockServiceContract queue(string $productId, int $quantity)
 * @method static StockServiceContract dequeue(string $productId, int $quantity)
 * @method static StockServiceContract transfer(string $productId, int $quantity)
 *
 * @see StockService
 */
class Stock extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'stock';
    }
}
