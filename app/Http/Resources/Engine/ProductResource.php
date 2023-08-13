<?php

namespace App\Http\Resources\Engine;

use App\Facades\Stock;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'version' => $this->version,
            'enabled' => $this->enabled,
            'sku' => $this->sku,
            'name' => $this->name,
            'type' => $this->type,
            'net_price' => $this->getPrice(),
            'gross_price' => $this->gross_price,
            'configuration' => $this->configuration,
            'stock' => Stock::get($this->id),
            'url' => $this->url,
            'image_url' => $this->image_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
