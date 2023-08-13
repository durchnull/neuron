<?php

namespace App\Http\Resources\Engine\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'company' => $this->company,
            'salutation' => $this->salutation,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'street' => $this->street,
            'number' => $this->number,
            'additional' => $this->getAttribute('additional'),
            'postal_code' => $this->postal_code,
            'city' => $this->city,
            'country_code' => $this->country_code,
        ];
    }
}
