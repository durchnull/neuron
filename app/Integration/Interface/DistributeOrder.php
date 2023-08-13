<?php

namespace App\Integration\Interface;

use App\Models\Engine\Order;

interface DistributeOrder
{
    public function distributeOrder(Order $order): void;
}
