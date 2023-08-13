<?php

namespace App\Actions\Engine\Merchant;

use App\Actions\Action;
use App\Models\Engine\Merchant;

abstract class MerchantAction extends Action
{
    final public static function targetClass(): string
    {
        return Merchant::class;
    }
}
