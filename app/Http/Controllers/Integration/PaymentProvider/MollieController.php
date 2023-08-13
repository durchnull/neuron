<?php

namespace App\Http\Controllers\Integration\PaymentProvider;

use App\Actions\Engine\Transaction\TransactionUpdateAction;
use App\Enums\TriggerEnum;
use App\Exceptions\Order\PolicyException;
use App\Facades\Integrations;
use App\Facades\SalesChannel;
use App\Http\Resources\Integration\PaymentProvider\MollieResource;
use App\Http\Response\Webhook\WebhookTransactionFailResponse;
use App\Http\Response\Webhook\WebhookTransactionSuccessResponse;
use App\Models\Engine\Order;
use App\Models\Engine\Transaction;
use App\Models\Integration\PaymentProvider\Mollie;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MollieController extends PaymentProviderController
{
    public static function getModelClass(): string
    {
        return Mollie::class;
    }

    public static function getResourceClass(): string
    {
        return MollieResource::class;
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
        $resourceId = $request->post('id');

        Log::channel('payment')->info('Mollie webhook ' . $resourceId);

        /** @var Order $order */
        $order = Order::with(['salesChannel', 'transactions'])
            ->whereHas('transactions', function (Builder $query) use ($orderId, $webhookId, $resourceId) {
                $query->where('resource_id', $resourceId)
                    ->where('webhook_id', $webhookId)
                    ->where('order_id', $orderId);
            })->firstOrFail();

        SalesChannel::set($order->salesChannel);

        $paymentProvider = Integrations::getPaymentProvider($order->payment->integration);

        $resource = $paymentProvider->getResource($resourceId);

        $status = $resource->getStatus();

        $transaction = $order->transactions->firstOrFail(
            fn(Transaction $transaction) => $transaction->resource_id === $resourceId
        );

        try {
            $action = new TransactionUpdateAction($transaction, [
                'status' => $status,
                'resource_data' => $resource->getData()
            ], TriggerEnum::Webhook);

            $action->trigger();
        } catch (ValidationException|PolicyException $exception) {
            Log::error($exception->getMessage());
            return new WebhookTransactionFailResponse();
        }

        return new WebhookTransactionSuccessResponse();
    }
}
