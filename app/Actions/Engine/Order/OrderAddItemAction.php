<?php

namespace App\Actions\Engine\Order;

use App\Enums\Order\OrderStatusEnum;
use App\Enums\Order\PolicyReasonEnum;
use App\Enums\Product\ProductTypeEnum;
use App\Facades\Order;
use App\Facades\Rule;
use App\Facades\Stock;
use App\Models\Engine\Item;
use App\Models\Engine\Product;
use Illuminate\Support\Facades\Log;

class OrderAddItemAction extends OrderAction
{
    public static function rules(): array
    {
        return [
            'order_id' => 'required|uuid|exists:orders,id',
            'reference' => 'nullable|uuid',
            'product_id' => 'required|uuid|exists:products,id,enabled,1',
            'quantity' => 'required|integer|min:1|max:1000',
            'configuration' => 'nullable|array|min:2', // @todo [rule][test] Configuration is required if product is of type bundle, add test
            'configuration.*' => 'required|uuid|exists:products,id,type,' . ProductTypeEnum::Product->value . ',enabled,1',
        ];
    }

    public function gate(array $attributes): void
    {
        parent::gate($attributes);

        $quantityInOrder = $this->target->items
            ->where(fn(Item $item) => $item->product_id === $attributes['product_id'])
            ->sum('quantity');

        // @todo [gate] load product type?
        $isBundle = isset($attributes['configuration']) && $attributes['configuration'] !== null;

        // @todo [test]
        if ($isBundle) {
            foreach ($attributes['configuration'] as $configurationProductId) {
                if (!Stock::has($configurationProductId, $quantityInOrder + $attributes['quantity'])) {
                    Log::error('No stock ' . $configurationProductId . ' ' . json_encode($attributes['configuration']));
                    $this->addPolicy(PolicyReasonEnum::OutOfStock);
                    break;
                }
            }
        } elseif (!Stock::has($attributes['product_id'], $quantityInOrder + $attributes['quantity'])) {
            Log::error('No stock ' . $attributes['product_id']);
            $this->addPolicy(PolicyReasonEnum::OutOfStock);
        }
    }

    public static function afterState(): array
    {
        return [OrderStatusEnum::Open];
    }

    protected function apply(): void
    {
        $product = Product::where('id', $this->validated['product_id'])->first();

        $item = Item::firstOrCreate([
            'order_id' => $this->target->id,
            'product_id' => $this->validated['product_id'],
            'reference' => $this->validated['reference'] ?? null,
            'configuration' => $this->validated['configuration'] ?? null,
        ], [
            'product_version' => $product->version,
            'total_amount' => $product->getPrice() * $this->validated['quantity'],
            'unit_amount' => $product->getPrice(),
            'discount_amount' => 0,
            'quantity' => $this->validated['quantity'],
            'position' => $this->target->items->count() + 1
        ]);

        if (!$item->wasRecentlyCreated) {
            $item->update(
                array_filter([
                    'total_amount' => $product->getPrice() * ($item->quantity + $this->validated['quantity']),
                    'quantity' => $item->quantity + $this->validated['quantity'],
                ])
            );
        }

        $this->target->setRelation('items', collect([$item])->concat($this->target->items->where('id', '!=', $item->id)));

        $this->target = Rule::apply($this->target);

        $this->target->update(
            array_merge(Order::getTotals($this->target), [
                'version' => $this->target->version + 1
            ])
        );

        $this->target = Order::updatePayment($this->target);
    }
}
