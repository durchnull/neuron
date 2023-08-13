<?php

namespace App\Services\Integration\PaymentProvider;

use App\Contracts\Integration\PaymentProvider\NeuronPaymentServiceContract;
use App\Enums\Payment\PaymentMethodEnum;
use App\Enums\Transaction\TransactionStatusEnum;
use App\Models\Engine\Order;
use App\Models\Engine\Transaction;
use App\Models\Integration\Integration;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use App\Services\Integration\PaymentProvider\Resources\NeuronPaymentResource;
use App\Services\Integration\PaymentProvider\Resources\Resource;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NeuronPaymentService implements NeuronPaymentServiceContract
{
    public function __construct(
        protected NeuronPayment $neuronPayment
    ) {
    }

    public static function getClientVersion(): string
    {
        return config('app.version');
    }

    /**
     * @throws Exception
     */
    public function createResource(Order $order, string $webhookId, array $resourceData = []): Resource
    {
        $status = TransactionStatusEnum::Created;

        if (!in_array($order->payment->method, static::getAllowedMethods())) {
            throw new Exception('Method not allowed for provider');
        }

        switch ($order->payment->method) {
            case PaymentMethodEnum::Free:
                $status = TransactionStatusEnum::Paid;
                break;
            case PaymentMethodEnum::Creditcard:
            case PaymentMethodEnum::Prepayment:
                $status = TransactionStatusEnum::Pending;
                break;
            default:
                throw new Exception('Method not allowed for provider');
        }

        return new NeuronPaymentResource(
            Str::uuid()->toString(),
            $this->getWebhookUrl($order->id, $webhookId),
            $status
        );
    }


    public function updateResource(string $id, Order $order, array $resourceData = []): Resource
    {
        // @todo
        $webhookId = Transaction::where('resource_id', $id)->value('webhook_id');

        return new NeuronPaymentResource(
            $id,
            $this->getWebhookUrl($order->id, $webhookId),
            TransactionStatusEnum::Created
        );
    }

    public function placeResource(string $id, Order $order, array $resourceData = []): Resource
    {
        $status = TransactionStatusEnum::Created;

        if (!in_array($order->payment->method, static::getAllowedMethods())) {
            throw new Exception('Method not allowed for provider');
        }

        switch ($order->payment->method) {
            case PaymentMethodEnum::Free:
                $status = TransactionStatusEnum::Paid;
                break;
            case PaymentMethodEnum::Creditcard:
            case PaymentMethodEnum::Prepayment:
                $status = TransactionStatusEnum::Pending;
                break;
            default:
                throw new Exception('Method not allowed for provider');
        }

        // @todo
        $webhookId = Transaction::where('resource_id', $id)->value('webhook_id');

        return new NeuronPaymentResource(
            $id,
            $this->getWebhookUrl($order->id, $webhookId),
            $status
        );
    }

    public function getResource(string $id): Resource
    {
        // @todo [query] Reconsider query
        /** @var Transaction $transaction */
        $transaction = Transaction::with('order')
            ->where('resource_id', $id)
            ->first();

        return new NeuronPaymentResource(
            $id,
            $this->getWebhookUrl($transaction->order->id, $transaction->webhook_id),
            $transaction->status,
        );
    }

    public function mapOrder(Order $order, string $webhookUrl): array
    {
        return [];
    }

    public function getWebhookUrl(string $orderId, string $webhookId): string
    {
        return route('integration.neuron-payment.transaction', [
            'orderId' => $orderId,
            'webhookId' => $webhookId,
        ]);
    }

    public static function getAllowedMethods(): array
    {
        $methods = [
            PaymentMethodEnum::Free,
            PaymentMethodEnum::Prepayment,
        ];

        if (!\Illuminate\Support\Facades\App::environment('production')) {
            $methods[] = PaymentMethodEnum::Creditcard;
        }

        return $methods;
    }

    public function getIntegration(): Integration
    {
        return $this->neuronPayment;
    }

    public function refundResource(string $id): Resource
    {
        Log::channel('integration')->info('Refund resource ' . $id);

        return new NeuronPaymentResource(
            $id,
            '', // @todo ?
            TransactionStatusEnum::Refunded,
        );
    }

    public function test(): bool
    {
        return true;
    }
}
