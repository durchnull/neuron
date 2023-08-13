<?php

namespace App\Models\Integration\PaymentProvider;

use App\Enums\Integration\IntegrationTypeEnum;
use App\Models\Integration\Integration;

abstract class PaymentProvider extends Integration
{
    public function getIntegrationType(): IntegrationTypeEnum
    {
        return IntegrationTypeEnum::PaymentProvider;
    }
}
