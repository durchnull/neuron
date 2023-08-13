<?php

namespace App\Models\Integration\Marketing;

use App\Enums\Integration\IntegrationTypeEnum;
use App\Models\Integration\Integration;

abstract class Marketing extends Integration
{
    public function getIntegrationType(): IntegrationTypeEnum
    {
        return IntegrationTypeEnum::Marketing;
    }
}
