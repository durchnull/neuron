<?php

namespace Tests;

use App\Enums\Transaction\TransactionStatusEnum;
use App\Facades\Integrations;
use App\Models\Engine\Transaction;
use App\Services\Integration\PaymentProvider\NeuronPaymentService;
use Exception;

trait Processing
{
    /**
     * @throws Exception
     */
    public function processPaymentProviderTransaction(
        string $transactionId,
        TransactionStatusEnum $status,
    ): void {
        /** @var Transaction $transaction */
        $transaction = Transaction::find($transactionId);

        $paymentProvider = Integrations::getPaymentProvider($transaction->integration);
        $transactionProviderWebhookUrl = $paymentProvider
            ->getWebhookUrl($transaction->order->id, $transaction->webhook_id);

        switch (get_class($paymentProvider)) {
            case NeuronPaymentService::class:
                $data = [
                    'status' => $status->value,
                    'resource_id' => $transaction->resource_id
                ];
                break;
            default:
                throw new Exception('Payment provider not allowed in tests');
        }

        $transactionWebhookResponse = $this->postJson($transactionProviderWebhookUrl, $data);

        $transactionWebhookResponse->assertStatus(200);
    }
}
