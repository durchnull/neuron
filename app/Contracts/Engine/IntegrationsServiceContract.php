<?php

namespace App\Contracts\Engine;

use App\Contracts\Integration\PaymentProvider\PaymentProviderServiceContract;
use App\Models\Engine\Order;
use App\Models\Integration\Integration;
use Illuminate\Support\Collection;

interface IntegrationsServiceContract
{
    public function getClasses(array $types = []): array;

    public function getModels(array $types = []): Collection;

    public function distributeOrder(Order $order, array $types = []): void;

    public function refundOrder(Order $order, array $types = []): void;

    public function cancelOrder(Order $order, array $types = []): void;

    public function getPaymentProvider(Integration $integration): PaymentProviderServiceContract;
}
