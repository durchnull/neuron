<?php

namespace App\Integration\Interface;

use App\Models\Engine\Order;

interface CancelOrder
{
    public function cancelOrder(Order $order): void;
}
