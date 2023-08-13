<?php

namespace App\Integration\Interface;

use App\Models\Engine\Order;

interface RefundOrder
{
    public function refundOrder(Order $order): void;
}
