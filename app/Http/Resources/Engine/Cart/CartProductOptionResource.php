<?php

namespace App\Http\Resources\Engine\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartProductOptionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'net_price' => $this->getPrice(),
            'configuration' => $this->configuration,
            'image_url' => $this->image_url,
        ];
    }
}
