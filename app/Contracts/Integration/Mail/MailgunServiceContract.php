<?php

namespace App\Contracts\Integration\Mail;

use App\Contracts\Integration\IntegrationServiceContract;
use App\Integration\Interface\DistributeOrder;

interface MailgunServiceContract extends MailServiceContract, IntegrationServiceContract, DistributeOrder
{
}
