<?php

namespace App\Enums\Integration;

enum IntegrationTypeEnum: string
{
    case PaymentProvider = 'payment-provider';

    case Inventory = 'inventory';

    case Mail = 'mail';

    case Marketing = 'marketing';
}
