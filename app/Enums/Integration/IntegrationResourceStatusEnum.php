<?php

namespace App\Enums\Integration;

enum IntegrationResourceStatusEnum: string
{
    case Created = 'created';

    case Distributed = 'distributed';

    case DistributedFailed = 'distribution-failed';
}
