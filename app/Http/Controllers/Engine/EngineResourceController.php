<?php

namespace App\Http\Controllers\Engine;

use App\Http\Controllers\ResourceController;

abstract class EngineResourceController extends ResourceController
{
    public static function getActionNamespace(): string
    {
        return 'App\Actions\Engine';
    }
}
