<?php

namespace App\Services\Integration\Inventory;

use BillbeeDe\BillbeeAPI\Client;

class BillbeeSandboxClient extends Client
{
    protected $endpoint = 'https://stage.billbee.io/api/v1/';
}
