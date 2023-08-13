<?php

namespace App\Http\Resources\Engine\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'configuration' => $this->configuration,
            'total_amount' => $this->total_amount,
            'unit_amount' => $this->unit_amount,
            'discount_amount' => $this->discount_amount,
            'quantity' => $this->quantity,
            'position' => $this->position,
        ];
    }
}
