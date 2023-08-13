<?php

namespace App\Http\Resources\Engine\Cart;

use App\Models\Engine\Transaction;
use App\Models\Integration\PaymentProvider\AmazonPay;
use App\Services\Integration\PaymentProvider\AmazonPayService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
