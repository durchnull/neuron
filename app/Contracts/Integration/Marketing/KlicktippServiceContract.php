<?php

namespace App\Contracts\Integration\Marketing;

use App\Contracts\Integration\IntegrationServiceContract;
use App\Integration\Interface\DistributeOrder;

interface KlicktippServiceContract extends MarketingServiceContract, IntegrationServiceContract, DistributeOrder
{

}
