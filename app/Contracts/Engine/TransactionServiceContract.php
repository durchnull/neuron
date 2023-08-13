<?php

namespace App\Contracts\Engine;

use App\Customer\CustomerProfile;
use App\Integration\Interface\RefundOrder;
use App\Integration\Interface\ShipOrder;
use App\Models\Engine\Order;
use App\Models\Engine\Transaction;

interface TransactionServiceContract extends RefundOrder, ShipOrder
{
    public function create(Order $order, array $resourceData = [], string $resourceId = null): Transaction;

    public function place(Order $order, array $resourceData = []): Transaction;

    public function cancel(Order $order): void;

    public function getCustomerProfile(Transaction $transaction): ?CustomerProfile;
}
