<?php

namespace App\Jobs;

use App\Enums\Transaction\TransactionStatusEnum;
use App\Models\Engine\Transaction;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentProviderNeuronWebhook // implements ShouldQueue // @todo [jobs]
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @throws Exception
     */
    public function __construct(
        public Transaction $transaction,
        public TransactionStatusEnum $status
    ) {
        if ($this->transaction->integration_type !== NeuronPayment::class) {
            throw new Exception('Webhook only works with NeuronPayment transactions');
        }
    }

    public function handle(): void
    {
        $response = Http::post(route('integration.neuron-payment.transaction', [
            'orderId' => $this->transaction->order_id,
            'webhookId' => $this->transaction->webhook_id,
        ]), [
            'resource_id' => $this->transaction->resource_id,
            'status' => $this->status->value
        ]);

        Log::channel('payment')->info($response->status());
        if (! $response->ok()) {
            Log::channel('payment')->info($response->body());
        }
    }
}
