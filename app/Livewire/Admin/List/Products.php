<?php

namespace App\Livewire\Admin\List;

use App\Models\Engine\Product;
use Illuminate\Database\Eloquent\Builder;

class Products extends View
{
    public array $search = [
        'name',
        'sku'
    ];

    public array $tableAttributes = [
        'enabled' => 'status',
        'name' => 'string',
        'type' => 'product_type',
        'sku' => 'string',
        'net_price' => 'money',
        'gross_price' => 'money',
        'prices' => 'product-prices',
        'stock' => 'stock',
        'inventoryable' => 'inventory',
        'version' => 'string',
    ];

    public function getBuilder(): Builder
    {
        return Product::with(['stock', 'inventoryable', 'prices']);
    }
}
