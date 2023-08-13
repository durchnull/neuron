<?php

namespace App\Services\Integration\PaymentProvider\Resources;

use App\Enums\Transaction\TransactionStatusEnum;

class NeuronPaymentResource extends Resource
{
    protected mixed $resource;

    public function __construct(
        protected string $id,
        protected string $webhookUrl,
        protected TransactionStatusEnum $status,
    ) {
        parent::__construct([
            'id' => $id,
            'webhook_url' => $webhookUrl,
            'status' => $this->status,
        ]);
    }

    public function getId(): string
    {
        return $this->resource['id'];
    }

    public function getCheckoutUrl(): string
    {
        return '';
    }

    public function getPaymentMethod(): string
    {
        return 'todo'; // @todo
    }

    public function getStatus(): TransactionStatusEnum
    {
        return $this->resource['status'];
    }

    public function close(): void
    {
        // Do nothing
    }

    public function refund(): void
    {
        // Do nothing
    }
}
