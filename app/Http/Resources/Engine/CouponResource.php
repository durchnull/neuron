<?php

namespace App\Http\Resources\Engine;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // @todo [resource] load relationshhip
        $this->rule;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'enabled' => $this->enabled,
            'rule' => RuleResource::make($this->whenLoaded('rule')),
        ];
    }
}
