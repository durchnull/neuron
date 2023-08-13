<?php

namespace App\Contracts\Integration\PaymentProvider;

use App\Contracts\Integration\IntegrationServiceContract;

interface PostFinanceServiceContract extends PaymentProviderServiceContract, IntegrationServiceContract
{
}
