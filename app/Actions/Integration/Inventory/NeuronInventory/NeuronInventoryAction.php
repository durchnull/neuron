<?php

namespace App\Actions\Integration\Inventory\NeuronInventory;

use App\Actions\Action;
use App\Models\Integration\Inventory\NeuronInventory;

abstract class NeuronInventoryAction extends Action
{
    final public static function targetClass(): string
    {
        return NeuronInventory::class;
    }
}
