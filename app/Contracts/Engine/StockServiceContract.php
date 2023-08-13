<?php

namespace App\Contracts\Engine;

use App\Models\Engine\Order;
use App\Models\Engine\Stock;

interface StockServiceContract
{
    public function queueOrder(Order $order): StockServiceContract;

    public function create(string $productId, int $quantity): Stock;

    /**
     * @param  string  $productId
     * @param  int  $quantity
     * @param  bool  $queue
     * @return bool
     */
    public function has(string $productId, int $quantity = 1, bool $queue = false): bool;

    /**
     * @param  string  $productId
     * @param  bool  $queue
     * @return int
     */
    public function get(string $productId, bool $queue = false): int;

    /**
     * Increase the stock value
     *
     * @param  string  $productId
     * @param  int  $quantity
     * @return StockServiceContract
     */
    public function add(string $productId, int $quantity): StockServiceContract;

    /**
     * Decrease the stock value
     *
     * @param  string  $productId
     * @param  int  $quantity
     * @return StockServiceContract
     */
    public function remove(string $productId, int $quantity): StockServiceContract;

    /**
     * Move a stock value to the queue
     *
     * @param  string  $productId
     * @param  int  $quantity
     * @return StockServiceContract
     */
    public function queue(string $productId, int $quantity): StockServiceContract;

    /**
     * Move a queue value back to the stock
     *
     * @param  string  $productId
     * @param  int  $quantity
     * @return StockServiceContract
     */
    public function dequeue(string $productId, int $quantity): StockServiceContract;

    /**
     * Decrease the queue value
     *
     * @param  string  $productId
     * @param  int  $quantity
     * @return StockServiceContract
     */
    public function transfer(string $productId, int $quantity): StockServiceContract;
}
