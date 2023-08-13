<?php

namespace App\Contracts\Integration\PaymentProvider;

use App\Contracts\Integration\IntegrationServiceContract;

interface PaypalServiceContract extends PaymentProviderServiceContract, IntegrationServiceContract
{
    public function refreshAccessToken(): void;
}
