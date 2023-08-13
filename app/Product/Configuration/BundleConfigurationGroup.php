<?php

namespace App\Product\Configuration;

use App\Models\Engine\Product;
use Illuminate\Contracts\Support\Arrayable;

class BundleConfigurationGroup implements Arrayable
{
    protected array $products;

    public function __construct()
    {
        $this->products = [];
    }

    public static function make(): BundleConfigurationGroup
    {
        return new static();
    }

    public function addProduct(Product $product): BundleConfigurationGroup
    {
        $this->products[] = $product;

        return $this;
    }

    public function toArray()
    {
        return array_map(
            fn(Product $product) => $product->id,
            $this->products
        );
    }
}
