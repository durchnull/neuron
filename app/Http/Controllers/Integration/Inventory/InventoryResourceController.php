<?php

namespace App\Http\Controllers\Integration\Inventory;

use App\Http\Controllers\ResourceController;

abstract class InventoryResourceController extends ResourceController
{
    public static function getActionNamespace(): string
    {
        return 'App\Actions\Integration\Inventory';
    }
}
