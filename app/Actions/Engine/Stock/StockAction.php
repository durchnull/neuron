<?php

namespace App\Actions\Engine\Stock;

use App\Actions\Action;
use App\Models\Engine\Stock;

abstract class StockAction extends Action
{
    final public static function targetClass(): string
    {
        return Stock::class;
    }
}
