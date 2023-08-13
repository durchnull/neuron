<?php

namespace App\Contracts\Integration\PaymentProvider;

use App\Models\Engine\Order;
use App\Models\Integration\Integration;
use App\Services\Integration\PaymentProvider\Resources\Resource;

interface PaymentProviderServiceContract
{
    public function getResource(string $id): Resource;

    public function createResource(Order $order, string $webhookId, array $resourceData = []): Resource;

    public function updateResource(string $id, Order $order, array $resourceData = []): Resource;

    public function placeResource(string $id, Order $order, array $resourceData = []): Resource;

    public function refundResource(string $id): Resource;

    public function mapOrder(Order $order, string $webhookUrl): array;

    public function getWebhookUrl(string $orderId, string $webhookId): string;

    public static function getAllowedMethods(): array;

    public function getIntegration(): Integration;

}
