<?php

namespace App\Actions\Engine\Product;

use App\Enums\Integration\IntegrationTypeEnum;
use App\Enums\Product\ProductTypeEnum;
use App\Facades\Integrations;
use App\Facades\Stock;
use Illuminate\Validation\Rules\Enum;

class ProductCreateAction extends ProductAction
{
    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid',
            'inventoryable_type' => 'required|in:' . implode(',', Integrations::getClasses([IntegrationTypeEnum::Inventory])),
            'inventoryable_id' => 'required|uuid',
            'inventory_id' => 'nullable|string',
            'type' => ['required', new Enum(ProductTypeEnum::class)],
            'enabled' => 'required|bool',
            'sku' => 'required|string',
            'ean' => 'nullable|string',
            'name' => 'required|string',
            'net_price' => 'required|integer|min:0',
            'gross_price' => 'required|integer|min:0',
            'configuration' => 'nullable|array|min:2',
            'configuration.*' => 'required|array|min:1',
            'configuration.*.*' => 'required|uuid|exists:products,id,type,' . ProductTypeEnum::Product->value,
            'url' => 'nullable|url',
            'image_url' => 'nullable|url|ends_with:jpg',
        ];
    }

    protected function gate(array $attributes): void
    {
    }

    protected function apply(): void
    {
        // @todo [test]
        if ($this->validated['type'] === ProductTypeEnum::Bundle->value && empty($this->validated['configuration'])) {
            $this->validated['enabled'] = false;
        }

        $this->target->fill([
            'sales_channel_id' => $this->validated['sales_channel_id'],
            'inventoryable_type' => $this->validated['inventoryable_type'],
            'inventoryable_id' => $this->validated['inventoryable_id'],
            'inventory_id' => $this->validated['inventory_id'],
            'enabled' => $this->validated['enabled'],
            'type' => $this->validated['type'],
            'sku' => $this->validated['sku'],
            'ean' => $this->validated['ean'] ?? null,
            'name' => $this->validated['name'],
            'net_price' => $this->validated['net_price'],
            'gross_price' => $this->validated['gross_price'],
            'configuration' => $this->validated['configuration']
                ?? ($this->validated['type'] === ProductTypeEnum::Bundle ? [] : null),
            'url' => $this->validated['url'] ?? null,
            'image_url' => $this->validated['image_url'] ?? null,
            'version' => 1,
        ])->save();

        if ($this->validated['type'] === ProductTypeEnum::Product->value) {
            $stock = Stock::create($this->target->id, 0);
            $this->target->stock()->save($stock);
        }
    }
}
