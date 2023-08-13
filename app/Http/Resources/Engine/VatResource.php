<?php

namespace App\Http\Resources\Engine;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VatResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vatable' => $this->vatable,
            'country_code' => $this->country_code,
            'rate' => $this->rate,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
