<?php

namespace App\Actions\Engine\SalesChannel;

use App\Actions\Action;
use App\Models\Engine\SalesChannel;

abstract class SalesChannelAction extends Action
{
    final public static function targetClass(): string
    {
        return SalesChannel::class;
    }
}
