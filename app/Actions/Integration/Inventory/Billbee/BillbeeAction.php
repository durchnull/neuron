<?php

namespace App\Actions\Integration\Inventory\Billbee;

use App\Actions\Action;
use App\Models\Integration\Inventory\Billbee;

abstract class BillbeeAction extends Action
{
    final public static function targetClass(): string
    {
        return Billbee::class;
    }
}
