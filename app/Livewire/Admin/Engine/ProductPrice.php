<?php

namespace App\Livewire\Admin\Engine;

use Livewire\Component;

class ProductPrice extends Component
{
    public string $enabled;
    public string $netPrice;
    public string $beginAt;
    public string $endAt;

    public function mount(string $id)
    {
        /** @var \App\Models\Engine\ProductPrice $productPrice */
        $productPrice = \App\Models\Engine\ProductPrice::find($id);

        $this->enabled = $productPrice->enabled;
        $this->netPrice = $productPrice->net_price;
        $this->beginAt = $productPrice->begin_at;
        $this->endAt = $productPrice->end_at;
    }

    public function render()
    {
        return view('livewire.admin.engine.product-price');
    }
}
