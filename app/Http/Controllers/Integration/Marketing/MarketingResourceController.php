<?php

namespace App\Http\Controllers\Integration\Marketing;

use App\Http\Controllers\ResourceController;

abstract class MarketingResourceController extends ResourceController
{
    public static function getActionNamespace(): string
    {
        return 'App\Actions\Integration\Marketing';
    }
}
