<?php

namespace App\Livewire\Admin\Engine;

use App\Facades\Stock;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Product extends Component
{
    public string $id;

    #[Rule('required|bool')]
    public string $enabled;

    #[Rule('required|string')]
    public string $name;

    #[Rule('required|string')]
    public string $sku;

    #[Rule('required|integer|min:0')]
    public int $netPrice;

    #[Rule('required|integer|min:0')]
    public int $grossPrice;

    public string $type;

    public string $inventoryType;

    public string $inventoryId;

    public int $version;

    public int $stock;

    public ?array $configuration;

    public array $productPriceIds;

    public function mount(string $id)
    {
        /** @var \App\Models\Engine\Product $product */
        $product = \App\Models\Engine\Product::with(['salesChannel', 'prices'])->find($id);

        $this->enabled = true;

        $this->id = $product->id;
        $this->name = $product->name;
        $this->type = $product->type->value;
        $this->sku = $product->sku;
        $this->version = $product->version;
        $this->inventoryType = $product->inventoryable_type;
        $this->inventoryId = $product->inventory_id;
        $this->configuration = $product->configuration;
        $this->netPrice = $product->net_price;
        $this->grossPrice = $product->gross_price;
        $this->stock = Stock::get($product->id);
        $this->productPriceIds = $product->prices->pluck('id')->toArray();
    }

    public function render()
    {
        return view('livewire.admin.engine.product');
    }
}
