<?php

namespace App\Http\Resources\Engine;

use App\Models\Engine\SalesChannel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesChannelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var SalesChannel $this */
        return [
            'id' => $this->id,
            'domains' => $this->domains,
            'name' => $this->name,
            'currency_code' => $this->currency_code,
            'locale' => $this->locale,
            'use_stock' => $this->use_stock,
            'remove_items_on_price_increase' => $this->remove_items_on_price_increase,
            'token' => $this->wasRecentlyCreated ? $this->token : null, // @todo reconsider
            'cart_token' => $this->wasRecentlyCreated ? $this->cart_token : null // @todo reconsider
        ];
    }
}
