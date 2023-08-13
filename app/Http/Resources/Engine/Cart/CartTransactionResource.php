<?php

namespace App\Http\Resources\Engine\Cart;

use App\Enums\Transaction\TransactionStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartTransactionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'status' => $this->status,
            'resource_id' => $this->resource_id, // @todo only amazon-pay?
            'method' => $this->method,
            'checkout_url' => in_array($this->status, [
                TransactionStatusEnum::Created,
                TransactionStatusEnum::Pending,
            ]) ? $this->checkout_url : null,
        ];
    }
}
