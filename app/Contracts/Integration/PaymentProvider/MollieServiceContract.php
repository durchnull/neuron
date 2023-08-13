<?php

namespace App\Contracts\Integration\PaymentProvider;

use App\Contracts\Integration\IntegrationServiceContract;

interface MollieServiceContract extends PaymentProviderServiceContract, IntegrationServiceContract
{
}
