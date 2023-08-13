<?php

namespace App\Actions\Engine\ProductPrice;

class ProductPriceCreateAction extends ProductPriceAction
{
    public static function rules(): array
    {
        return [
            'product_id' => 'required|uuid|exists:products,id',
            'net_price' => 'required|integer|min:0',
            'gross_price' => 'required|integer|min:0',
            'begin_at' => 'required|date',
            'end_at' => 'required|date',
            'enabled' => 'required|boolean',
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
