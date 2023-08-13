<?php

namespace App\Services\Engine;

use App\Actions\Engine\Transaction\TransactionCreateAction;
use App\Actions\Engine\Transaction\TransactionUpdateAction;
use App\Contracts\Engine\IntegrationsServiceContract;
use App\Contracts\Engine\TransactionServiceContract;
use App\Customer\CustomerProfile;
use App\Enums\Transaction\TransactionStatusEnum;
use App\Enums\TriggerEnum;
use App\Exceptions\Order\PolicyException;
use App\Models\Engine\Order;
use App\Models\Engine\Transaction;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TransactionService implements TransactionServiceContract
{
    public function __construct(
        protected IntegrationsServiceContract $integrationsService
    ) {
    }

    /**
     * @param  Order  $order
     * @param  array  $resourceData
     * @param  string|null  $resourceId
     * @return Transaction
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function create(Order $order, array $resourceData = [], string $resourceId = null): Transaction
    {
        $paymentProvider = $this->integrationsService->getPaymentProvider($order->payment->integration);

        if ($resourceId) {
            // Resource is updated but a new neuron transaction will be created with it
            $resource = $paymentProvider->updateResource(
                $resourceId,
                $order,
                $resourceData
            );

            $webhookId = null;
        } else {
            $webhookId = Str::uuid()->toString();

            $resource = $paymentProvider->createResource(
                $order,
                $webhookId,
                $resourceData
            );
        }

        $integration = $paymentProvider->getIntegration();

        $transactionCreateAction = new TransactionCreateAction(new Transaction(), [
            'sales_channel_id' => $order->sales_channel_id,
            'integration_id' => $integration->id,
            'integration_type' => get_class($integration),
            'order_id' => $order->id,
            'status' => $resource->getStatus(),
            'method' => $resource->getPaymentMethod(),
            'resource_id' => $resource->getId(),
            'resource_data' => $resource->getData(),
            'webhook_id' => $webhookId,
            'checkout_url' => $resource->getCheckoutUrl(),
        ], TriggerEnum::App);

        $transactionCreateAction->trigger();

        /** @var Transaction $transaction */
        $transaction = $transactionCreateAction->target();

        return $transaction;
    }

    /**
     * @param  Order  $order
     * @param  array  $resourceData
     * @param  string|null  $resourceId
     * @return Transaction
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function update(Order $order, string $resourceId, array $resourceData = []): Transaction
    {
        $paymentProvider = $this->integrationsService->getPaymentProvider($order->payment->integration);

        $resource = $paymentProvider->updateResource(
            $resourceId,
            $order,
            $resourceData
        );

        $transactionCreateAction = new TransactionUpdateAction(new Transaction(), [
            'status' => $resource->getStatus(),
            'method' => $resource->getPaymentMethod(),
            'resource_data' => $resource->getData(),
        ], TriggerEnum::App);

        $transactionCreateAction->trigger();

        /** @var Transaction $transaction */
        $transaction = $transactionCreateAction->target();

        return $transaction;
    }

    /**
     * @param  Order  $order
     * @param  array  $resourceData
     * @return Transaction
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function place(Order $order, array $resourceData = []): Transaction
    {
        $paymentProvider = $this->integrationsService->getPaymentProvider($order->payment->integration);

        $placeableTransactions = $order->transactions->filter(
            fn(Transaction $transaction) => $transaction->status === TransactionStatusEnum::Created
        );

        if ($placeableTransactions->isEmpty()) {
            return $this->create($order, $resourceData);
        } elseif ($placeableTransactions->count() === 1) {
            $transaction = $placeableTransactions->first();

            $resource = $paymentProvider->placeResource(
                $transaction->resource_id,
                $order,
                $resourceData
            );

            $action = new TransactionUpdateAction($transaction, [
                'status' => $resource->getStatus(),
                'method' => $resource->getPaymentMethod(),
                'resource_data' => $resource->getData()
            ], TriggerEnum::App, true);

            $action->trigger();

            return $action->target();
        } else {
            throw new Exception('Placeable transactions count mismatch');
        }
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    public function cancel(Order $order): void
    {
        foreach ($order->transactions as $transaction) {
            $transaction->load('integration');
            $paymentProvider = $this->integrationsService->getPaymentProvider($transaction->integration);
            $resource = $paymentProvider->getResource($transaction->resource_id);

            // @todo implement all closing methods
            $resource->close();

            if ($transaction->status !== $resource->getStatus()) {
                $transactionCreateAction = new TransactionUpdateAction($transaction, [
                    'status' => $resource->getStatus(),
                ], TriggerEnum::App);

                $transactionCreateAction->trigger();
            }
        }
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    // @todo [test]
    public function refundOrder(Order $order): void
    {
        // @todo fetch update before?
        // @todo cancel or close others transactions?
        $refundableTransactions = $order->transactions->filter(
            fn(Transaction $transaction) => in_array($transaction->status, [
                TransactionStatusEnum::Paid,
            ])
        );

        /** @var Transaction $transaction */
        foreach ($refundableTransactions as $transaction) {
            $paymentProvider = $this->integrationsService->getPaymentProvider($transaction->integration);

            $resource = $paymentProvider->refundResource($transaction->resource_id);

            if ($transaction->status !== $resource->getStatus()) {
                $transactionUpdateAction = new TransactionUpdateAction($transaction, [
                    'status' => $resource->getStatus(),
                ], TriggerEnum::App);

                $transactionUpdateAction->trigger();

                $transaction = $transactionUpdateAction->target();

                \App\Facades\Order::updateStatus($transaction->order);
            }
        }
    }

    public function shipOrder(Order $order): void
    {
        // TODO: Implement shipOrder() method.
    }

    public function getCustomerProfile(Transaction $transaction): ?CustomerProfile
    {
        return $this->integrationsService
            ->getPaymentProvider($transaction->integration)
            ->getResource($transaction->resource_id)
            ->getCustomerProfile();
    }
}
