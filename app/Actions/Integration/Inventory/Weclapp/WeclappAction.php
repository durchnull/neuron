<?php

namespace App\Actions\Integration\Inventory\Weclapp;

use App\Actions\Action;
use App\Models\Integration\Inventory\Weclapp;

abstract class WeclappAction extends Action
{
    final public static function targetClass(): string
    {
        return Weclapp::class;
    }
}
