<?php

namespace App\Actions\Engine\Order;

use App\Enums\Order\OrderStatusEnum;
use App\Enums\Order\PolicyReasonEnum;
use App\Facades\Order;
use App\Facades\Rule;
use App\Facades\Stock;
use App\Models\Engine\Item;

class OrderUpdateItemAction extends OrderAction
{
    public static function rules(): array
    {
        return [
            'order_id' => 'required|uuid|exists:orders,id',
            'order_item_id' => 'required|uuid|exists:order_items,id',
            'product_version' => 'required|integer|min:2',
            'total_amount' => 'required|integer|min:0',
            'unit_amount' => 'required|integer|min:0',
        ];
    }

    protected function gate(array $attributes): void
    {
        parent::gate($attributes);

        // @todo [test]
        if (!(isset($attributes['reference_lock']) && $attributes['reference_lock'] === false)) {
            $item = $this->target->items->first(fn(Item $item) => $item->id === $attributes['order_item_id']);

            if ($item->reference !== null) {
                $this->addPolicy(PolicyReasonEnum::ItemLocked);
            }
        }

        $item = $this->target->items->first(
            fn(Item $item) => $item->id === $attributes['order_item_id']
        );

        // @todo [test]
        if ($item && isset($attributes['quantity'])) {
            $quantityInOrderExceptFromUpdateableItem = $this->target->items
                ->where(fn(Item $_item) => $_item->id !== $attributes['order_item_id'] && $_item->product_id === $item->product_id)
                ->sum('quantity');

            if (!Stock::has($item->product_id, $quantityInOrderExceptFromUpdateableItem + $attributes['quantity'])) {
                $this->addPolicy(PolicyReasonEnum::OutOfStock);
            }
        }
    }

    public static function afterState(): array
    {
        return [OrderStatusEnum::Open];
    }

    protected function apply(): void
    {
        $item = $this->target->items->first(fn(Item $item) => $item->id === $this->validated['order_item_id']);

        if ($item) {
            $item->update([
                'product_version' => $this->validated['product_version'],
                'total_amount' => $this->validated['total_amount'],
                'unit_amount' => $this->validated['unit_amount'],
            ]);

            $this->target->setRelation('items', collect([$item])->concat($this->target->items->where('id', '!=', $item->id)));

            $this->target = Rule::apply($this->target);

            $this->target->update(
                array_merge(Order::getTotals($this->target), [
                    'version' => $this->target->version + 1,
                ])
            );

            $this->target = Order::updatePayment($this->target);
        }
    }
}
