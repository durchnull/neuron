<?php

namespace App\Enums;

enum TriggerEnum: string
{
    case App = 'app';
    case Api = 'api';
    case Webhook = 'webhook';
    case Admin = 'admin';
    case Console = 'console';
    case Customer = 'customer';
    case Rule = 'rule';
}
