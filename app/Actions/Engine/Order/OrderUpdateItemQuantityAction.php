<?php

namespace App\Actions\Engine\Order;

use App\Enums\Order\OrderStatusEnum;
use App\Enums\Order\PolicyReasonEnum;
use App\Facades\Order;
use App\Facades\Rule;
use App\Facades\Stock;
use App\Models\Engine\Item;

class OrderUpdateItemQuantityAction extends OrderAction
{
    public static function rules(): array
    {
        return [
            'order_id' => 'required|uuid|exists:orders,id',
            'order_item_id' => 'required|uuid|exists:order_items,id',
            'quantity' => 'nullable|integer|min:1|max:1000',
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

        if ($item && isset($attributes['quantity'])) {
            $quantityInOrderExceptFromUpdatableItem = $this->target->items
                ->where(fn(Item $_item) => $_item->id !== $attributes['order_item_id'] && $_item->product_id === $item->product_id)
                ->sum('quantity');

            if (!Stock::has($item->product_id, $quantityInOrderExceptFromUpdatableItem + $attributes['quantity'])) {
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
                'total_amount' => $item->unit_amount * $this->validated['quantity'],
                'quantity' => $this->validated['quantity'],
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
