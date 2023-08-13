<?php

namespace App\Contracts\Integration\Inventory;

use App\Contracts\Integration\IntegrationServiceContract;
use App\Integration\Interface\DistributeOrder;
use App\Integration\Interface\ReceiveInventory;

interface BillbeeServiceContract extends InventoryServiceContract, IntegrationServiceContract, DistributeOrder, ReceiveInventory
{

}
