<?php

namespace App\Actions\Engine\Order;

use App\Enums\Order\OrderStatusEnum;
use App\Enums\Order\PolicyReasonEnum;
use App\Facades\Order;
use App\Facades\Rule;
use App\Models\Engine\Item;

class OrderRemoveItemAction extends OrderAction
{
    public static function rules(): array
    {
        return [
            'order_id' => 'required|uuid|exists:orders,id',
            'order_item_id' => 'required|uuid|exists:order_items,id',
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
    }

    public static function afterState(): array
    {
        return [OrderStatusEnum::Open];
    }

    protected function apply(): void
    {
        if (Item::destroy($this->validated['order_item_id']) === 1) {
            $this->target->setRelation('items', collect()->concat($this->target->items->where('id', '!=', $this->validated['order_item_id'])));

            $this->target = Rule::apply($this->target);

            $this->target->update(
                array_merge(Order::getTotals($this->target), [
                    'version' => $this->target->version + 1
                ])
            );

            $this->target = Order::updatePayment($this->target);
        }
    }
}
