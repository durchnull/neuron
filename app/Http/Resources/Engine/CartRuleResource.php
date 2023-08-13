<?php

namespace App\Http\Resources\Engine;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartRuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'enabled' => $this->enabled,
            'rule' => RuleResource::make($this->whenLoaded('rule')),
        ];
    }
}
