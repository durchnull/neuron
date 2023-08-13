<?php

namespace App\Contracts\Integration\PaymentProvider;

use App\Contracts\Integration\IntegrationServiceContract;
use App\Models\Engine\Order;

interface AmazonPayServiceContract extends PaymentProviderServiceContract, IntegrationServiceContract
{
    public static function getAlgorithm(): string;

    public function makeCheckoutSessionPayload(Order $order): array;

    public function generateButtonSignature(array $payload): string;
}
