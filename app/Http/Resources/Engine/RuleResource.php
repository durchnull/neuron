<?php

namespace App\Http\Resources\Engine;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'consequences' => $this->consequences->toArray(),
            'condition' => ConditionResource::make($this->whenLoaded('condition')),
            'position' => $this->position
        ];
    }
}
