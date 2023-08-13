<?php

namespace App\Integration\Interface;

use App\Models\Engine\Order;

interface ShipOrder
{
    public function shipOrder(Order $order): void;
}
