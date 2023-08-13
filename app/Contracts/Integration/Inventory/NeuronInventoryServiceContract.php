<?php

namespace App\Contracts\Integration\Inventory;

use App\Contracts\Integration\IntegrationServiceContract;
use App\Integration\Interface\DistributeOrder;

interface NeuronInventoryServiceContract extends InventoryServiceContract, IntegrationServiceContract, DistributeOrder
{
}
