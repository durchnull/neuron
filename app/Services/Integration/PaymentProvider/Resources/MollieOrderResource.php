<?php

namespace App\Services\Integration\PaymentProvider\Resources;

use App\Enums\Transaction\TransactionStatusEnum;
use Exception;
use Illuminate\Support\Facades\Log;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Types\OrderStatus;
use Mollie\Api\Types\PaymentStatus;

class MollieOrderResource extends Resource
{
    public function __construct(protected mixed $resource, array $data = [])
    {
        parent::__construct($resource, $data);

        if ($paymentCollection = $this->resource->payments()) {

            $this->data['payments'] = [];

            /** @var Payment $payment */
            foreach ($paymentCollection as $payment) {
                $this->data['payments'][] = [
                    'id' => $payment->id,
                    'status' => $payment->status,
                ];
            }
        }
    }

    public function getId(): string
    {
        return $this->resource->id;
    }

    public function getCheckoutUrl(): string
    {
        return $this->resource->getCheckoutUrl();
    }

    /**
     * @throws Exception
     */
    public function getPaymentMethod(): string
    {
        return $this->getLatestPayment()->method;
    }

    /**
     * @throws Exception
     */
    public function getStatus(): TransactionStatusEnum
    {
        if ($this->resource->status === OrderStatus::STATUS_CREATED) {
            $latestPayment = $this->getLatestPayment();

            return match ($latestPayment->status) {
                PaymentStatus::STATUS_OPEN => TransactionStatusEnum::Created,
                PaymentStatus::STATUS_PENDING => TransactionStatusEnum::Pending,
                PaymentStatus::STATUS_AUTHORIZED => TransactionStatusEnum::Authorized,
                PaymentStatus::STATUS_CANCELED, PaymentStatus::STATUS_EXPIRED => TransactionStatusEnum::Canceled,
                PaymentStatus::STATUS_PAID => TransactionStatusEnum::Paid,
                PaymentStatus::STATUS_FAILED => TransactionStatusEnum::Failed,
            };
        }

        return match ($this->resource->status) {
            OrderStatus::STATUS_PAID, OrderStatus::STATUS_COMPLETED, OrderStatus::STATUS_SHIPPING => TransactionStatusEnum::Paid,
            OrderStatus::STATUS_AUTHORIZED => TransactionStatusEnum::Authorized,
            OrderStatus::STATUS_CANCELED, OrderStatus::STATUS_EXPIRED => TransactionStatusEnum::Canceled,
            OrderStatus::STATUS_PENDING => TransactionStatusEnum::Pending,
            OrderStatus::STATUS_REFUNDED => TransactionStatusEnum::Refunded,
            default => throw new Exception('Status not handled ' . $this->resource->status)
        };
    }

    /**
     * @throws ApiException
     */
    public function close(): void
    {
        $this->resource->cancel();
    }

    public function refund(): void
    {
        $this->resource->refundAll();
    }

    private function getLatestPayment(): ?Payment
    {
        /** @var PaymentCollection $paymentCollection */
        $paymentCollection = $this->resource->payments();

        if ($paymentCollection->count() === 0) {
            throw new Exception('Order should have at least one payment');
        }

        $latestPayment = null;

        /** @var Payment $payment */
        foreach ($paymentCollection as $payment) {
            if ($latestPayment === null || $payment->createdAt > $latestPayment->createdAt) {
                $latestPayment = $payment;
            }
            Log::channel('payment')->info($payment->id . ' ' . $payment->status);
        }

        return $latestPayment;
    }
}
