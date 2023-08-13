<?php

namespace App\Http\Resources\Engine;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingResource extends JsonResource
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
            'enabled' => $this->enabled,
            'name' => $this->name,
            'country_code' => $this->country_code,
            'net_price' => $this->net_price,
            'gross_price' => $this->gross_price,
            'currency_code' => $this->currency_code,
            'position' => $this->position,
        ];
    }
}
