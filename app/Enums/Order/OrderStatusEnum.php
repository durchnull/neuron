<?php

namespace App\Enums\Order;

enum OrderStatusEnum: string
{
    case Open = 'open';
    case Placing = 'placing';
    case Accepted = 'accepted';
    case Confirmed = 'confirmed';
    case Shipped = 'shipped';
    case Refunded = 'refunded';
    case Canceled = 'canceled';
}
