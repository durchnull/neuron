<?php

namespace App\Models\Integration\Mail;

use App\Enums\Integration\IntegrationTypeEnum;
use App\Models\Integration\Integration;

abstract class Mail extends Integration
{
    public function getIntegrationType(): IntegrationTypeEnum
    {
        return IntegrationTypeEnum::Mail;
    }
}
