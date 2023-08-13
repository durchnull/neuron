<?php

namespace App\Actions\Engine\Product;

use App\Enums\Product\ProductTypeEnum;

class ProductUpdateAction extends ProductAction
{
    public static function rules(): array
    {
        return [
            'enabled' => 'nullable|boolean',
            'sku' => 'nullable|string',
            'name' => 'nullable|string',
            'net_price' => 'nullable|integer|min:0',
            'gross_price' => 'nullable|integer|min:0',
            'configuration' => 'nullable|array|min:2',
            'configuration.*' => 'required|array|min:1',
            'configuration.*.*' => 'required|uuid|exists:products,id,type,' . ProductTypeEnum::Product->value,
            'url' => 'nullable|url',
            'image_url' => 'nullable|url|ends_with:jpg',
        ];
    }

    protected function apply(): void
    {
        // @todo [test]
        if ($this->target->type === ProductTypeEnum::Bundle && empty($this->validated['configuration'])) {
            $this->validated['enabled'] = false;
        }

        $this->target->fill($this->validated);

        if ($this->target->isDirty()) {
            $this->target->fill([
                'version' => $this->target->version + 1,
            ])->save();
        }
    }
}
