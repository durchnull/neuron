<?php

namespace App\Services\Integration\PaymentProvider\Resources;

use App\Customer\CustomerProfile;
use App\Enums\Transaction\TransactionStatusEnum;

abstract class Resource
{
    protected array $data;

    public function __construct(protected mixed $resource, array $data = [])
    {
        $this->data = [];
    }

    abstract public function getId(): string;

    abstract public function getCheckoutUrl(): string;

    abstract public function getPaymentMethod(): string;

    abstract public function getStatus(): TransactionStatusEnum;

    abstract public function close(): void;

    abstract public function refund(): void;

    public function getResource(): mixed
    {
        return $this->resource;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getCustomerProfile(): ?CustomerProfile
    {
        return null;
    }
}
