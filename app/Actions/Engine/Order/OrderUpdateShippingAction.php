<?php

namespace App\Actions\Engine\Order;

use App\Enums\Order\OrderStatusEnum;
use App\Facades\Order;
use App\Facades\Rule;

class OrderUpdateShippingAction extends OrderAction
{
    public static function rules(): array
    {
        return [
            'order_id' => 'required|uuid|exists:orders,id',
            'shipping_id' => 'nullable|uuid|exists:shippings,id,enabled,1',
        ];
    }

    protected function gate(array $attributes): void
    {
        parent::gate($attributes);

        if ($this->target->shipping_address && $this->target->shipping_address->country_code) {
            // @todo
            // new shipping country and shipping address country mismatch
        }
    }

    public static function afterState(): array
    {
        return [OrderStatusEnum::Open];
    }

    protected function apply(): void
    {
        $attributes = array_filter([
            'shipping_id' => $this->getStringDifferenceOrNull($this->target->shipping_id, $this->validated['shipping_id'] ?? null),
        ]);

        if (!empty($attributes)) {
            $this->target->fill($attributes);
            $this->target->load('shipping');
            $this->target->shipping_amount = $this->target->shipping->net_price;
            $this->target->shipping_discount_amount = 0;

            $this->target = Rule::apply($this->target);

            $this->target->update(
                array_merge(Order::getTotals($this->target), [
                    'version' => $this->target->version + 1,
                ], $attributes)
            );

            $this->target = Order::updatePayment($this->target);
        }
    }
}
