<?php

namespace App\Actions\Integration\PaymentProvider\PostFinance;

use App\Actions\Action;
use App\Models\Integration\PaymentProvider\PostFinance;

abstract class PostFinanceAction extends Action
{
    final public static function targetClass(): string
    {
        return PostFinance::class;
    }
}
