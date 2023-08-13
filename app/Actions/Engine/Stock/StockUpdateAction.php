<?php

namespace App\Actions\Engine\Stock;

class StockUpdateAction extends StockAction
{
    public static function rules(): array
    {
        return [
            'product_id' => 'required|uuid|exists:stock',
            'value' => 'nullable|integer|min:0',
            'queue' => 'nullable|integer|min:0',
        ];
    }

    protected function apply(): void
    {
        $attributes = array_filter([
            'value' => $this->validated['value'] ?? null,
            'queue' => $this->validated['queue'] ?? null,
        ], 'is_numeric');

        if (!empty($attributes)) {
            $this->target->update($attributes);
        }
    }
}
