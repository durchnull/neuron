<?php

namespace App\Actions\Engine\Stock;

use App\Enums\Product\ProductTypeEnum;

class StockCreateAction extends StockAction
{
    public static function rules(): array
    {
        return [
            'product_id' => 'required|uuid|exists:products,id,type,' . ProductTypeEnum::Product->value,
            'value' => 'nullable|integer|min:0',
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
