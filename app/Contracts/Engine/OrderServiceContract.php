<?php

namespace App\Contracts\Engine;

use App\Models\Engine\Order;

interface OrderServiceContract
{
    public function set(Order $order): OrderServiceContract;

    public function setById(string $id): OrderServiceContract;

    public function get(): Order;

    public function open(): bool;

    public function getTotals(Order $order): array;

    public function update(Order $order): Order;

    public function updateItems(Order $order): Order;

    public function updateStatus(Order $order): Order;

    public function updatePayment(Order $order): Order;

    public function generateOrderNumber(): string;
}
