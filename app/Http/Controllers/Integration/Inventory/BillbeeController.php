<?php

namespace App\Http\Controllers\Integration\Inventory;

use App\Http\Resources\Integration\Inventory\BillbeeResource;
use App\Livewire\Admin\Integration\Billbee;

class BillbeeController extends InventoryResourceController
{
    public function entry()
    {
        // @todo
    }

    public static function getModelClass(): string
    {
        return Billbee::class;
    }

    public static function getResourceClass(): string
    {
        return BillbeeResource::class;
    }
}
