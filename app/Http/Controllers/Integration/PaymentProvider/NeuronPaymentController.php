<?php

namespace App\Http\Controllers\Integration\PaymentProvider;

use App\Actions\Engine\Transaction\TransactionUpdateAction;
use App\Enums\TriggerEnum;
use App\Exceptions\Order\PolicyException;
use App\Facades\SalesChannel;
use App\Http\Resources\Integration\PaymentProvider\NeuronPaymentResource;
use App\Http\Response\Webhook\WebhookTransactionFailResponse;
use App\Http\Response\Webhook\WebhookTransactionSuccessResponse;
use App\Models\Engine\Order;
use App\Models\Engine\Transaction;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class NeuronPaymentController extends PaymentProviderController
{
    public static function getModelClass(): string
    {
        return NeuronPayment::class;
    }

    public static function getResourceClass(): string
    {
        return NeuronPaymentResource::class;
    }

    /**
     * @param  Request  $request
     * @param  string  $orderId
     * @param  string  $webhookId
     * @return JsonResponse
     * @throws Exception
     */
    public function transaction(Request $request, string $orderId, string $webhookId): JsonResponse
    {
        Log::channel('payment')->info('NeuronPaymentController ' . $orderId . ' ' . $webhookId);

        $status = $request->post('status');
        $resourceId = $request->post('resource_id');

        // @todo [validation]

        /** @var Order $order */
        $order = Order::with(['salesChannel', 'transactions'])
            ->whereHas('transactions', function (Builder $query) use ($orderId, $webhookId, $resourceId) {
                $query->where('resource_id', $resourceId)
                    ->where('webhook_id', $webhookId)
                    ->where('order_id', $orderId);
            })->firstOrFail();

        SalesChannel::set($order->salesChannel);

        $transaction = $order->transactions->firstOrFail(
            fn(Transaction $transaction) => $transaction->resource_id === $resourceId
        );

        try {
            $action = new TransactionUpdateAction($transaction, [
                'status' => $status,
            ], TriggerEnum::Api);

            $action->trigger();
        } catch (ValidationException|PolicyException $exception) {
            return new WebhookTransactionFailResponse();
        }

        return new WebhookTransactionSuccessResponse();
    }

    // </editor-fold>
}
