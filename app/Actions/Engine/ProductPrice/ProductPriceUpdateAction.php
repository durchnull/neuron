<?php

namespace App\Actions\Engine\ProductPrice;

class ProductPriceUpdateAction extends ProductPriceAction
{
    public static function rules(): array
    {
        return [
            'net_price' => 'nullable|integer|min:0',
            'gross_price' => 'nullable|integer|min:0',
            'begin_at' => 'nullable|date',
            'end_at' => 'nullable|date',
            'enabled' => 'nullable|boolean',
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
