<?php

namespace App\Services\Integration\PaymentProvider;


use App\Contracts\Integration\PaymentProvider\PostFinanceServiceContract;
use App\Models\Engine\Order;
use App\Models\Integration\Integration;
use App\Models\Integration\PaymentProvider\PostFinance;
use App\Services\Integration\PaymentProvider\Resources\PostFinanceResource;
use App\Services\Integration\PaymentProvider\Resources\Resource;
use PostFinanceCheckout\Sdk\ApiClient;

/**
 * @link https://checkout.postfinance.ch/doc/api/web-service
 */
class PostFinanceService implements PostFinanceServiceContract
{
    protected ApiClient $client;

    public function __construct(
        protected PostFinance $postFinance
    ) {
        $this->client = new ApiClient(
            $this->postFinance->user_id,
            $this->postFinance->secret
        );
    }

    public static function getClientVersion(): string
    {
        // ApiClient::$defaultHeaders['x-meta-sdk-version']
        return '4.4.0';
    }

    public function test(): bool
    {

    }

    public function getResource(string $id): Resource
    {
        return new PostFinanceResource([]);  // @todo [implementation]
    }

    public function createResource(Order $order, string $webhookId, array $resourceData = []): Resource
    {
        return new PostFinanceResource([]);  // @todo [implementation]
    }

    public function updateResource(string $id, Order $order, array $resourceData = []): Resource
    {
        // TODO: Implement updateResource() method.
    }

    public function placeResource(string $id, Order $order, array $resourceData = []): Resource
    {
        // TODO: Implement placeResource() method.
    }

    public function refundResource(string $id): Resource
    {
        // TODO: Implement refundResource() method.
    }

    public function mapOrder(Order $order, string $webhookUrl): array
    {
        return []; // @todo [implementation]
    }

    public function getWebhookUrl(string $orderId, string $webhookId): string
    {
        return ''; // @todo [implementation]
    }

    public static function getAllowedMethods(): array
    {
        return [
            // @todo [implementation]
        ];
    }

    public function getIntegration(): Integration
    {
        return $this->postFinance;
    }
}
