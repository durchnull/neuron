<?php

namespace App\Contracts\Engine;

use App\Models\Engine\Coupon;
use App\Models\Engine\Order;

interface CouponServiceContract
{
    public function generateCode(): string;

    public function getByCode(string $code): ?Coupon;

    public function chargeCredit(Order $order): Order;
}
