<?php

namespace App\Actions\Engine\Transaction;

use App\Actions\Action;
use App\Models\Engine\Transaction;

abstract class TransactionAction extends Action
{
    final public static function targetClass(): string
    {
        return Transaction::class;
    }
}
