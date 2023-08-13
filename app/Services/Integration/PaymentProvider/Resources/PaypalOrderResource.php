<?php

namespace App\Services\Integration\PaymentProvider\Resources;

use App\Enums\Transaction\TransactionStatusEnum;
use Exception;

class PaypalOrderResource extends Resource
{
    public function getId(): string
    {
        return $this->resource['id'];
    }

    public function getCheckoutUrl(): string
    {
        $links = array_filter(
            $this->resource['links'],
            fn(array $link) => $link['rel'] === 'payer-action'
        );

        if (count($links) === 1) {
            return $links[0]['href'];
        }

        return ''; // @todo
    }

    public function getPaymentMethod(): string
    {
        return ''; // @todo
    }

    /**
     * @throws Exception
     */
    public function getStatus(): TransactionStatusEnum
    {
        return match ($this->resource['status']) {
            'PAYER_ACTION_REQUIRED' => TransactionStatusEnum::Pending,
            default => throw new Exception('Status not handled ' . $this->resource['status'])
        };
    }

    public function close(): void
    {
        // @todo
    }

    public function refund(): void
    {
        // @todo
    }
}
