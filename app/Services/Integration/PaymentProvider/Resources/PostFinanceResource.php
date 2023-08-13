<?php

namespace App\Services\Integration\PaymentProvider\Resources;

use App\Customer\CustomerProfile;
use App\Enums\Transaction\TransactionStatusEnum;

class PostFinanceResource extends Resource
{

    public function __construct(protected mixed $resource, array $data = [])
    {
        parent::__construct($resource, $data);
    }

    public function getId(): string
    {
        return '';
    }

    public function getCheckoutUrl(): string
    {
        return '';
    }

    public function getPaymentMethod(): string
    {
        return ''; // @todo
    }

    public function getStatus(): TransactionStatusEnum
    {
        return TransactionStatusEnum::Created;
    }

    public function close(): void
    {
    }

    public function refund(): void
    {
    }
}
