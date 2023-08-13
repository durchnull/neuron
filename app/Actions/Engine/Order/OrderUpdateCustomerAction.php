<?php

namespace App\Actions\Engine\Order;

use App\Enums\Order\OrderStatusEnum;
use App\Facades\Order;
use App\Facades\Rule;

class OrderUpdateCustomerAction extends OrderAction
{
    public static function rules(): array
    {
        return [
            'order_id' => 'required|uuid|exists:orders,id',
            'customer_id' => 'nullable|uuid|exists:customers,id',
            'billing_address_id' => 'nullable|uuid|exists:addresses,id',
            'shipping_address_id' => 'nullable|uuid|exists:addresses,id',
            'customer_note' => 'nullable|string' // @todo [validation] string = max:255 ?
        ];
    }

    public static function afterState(): array
    {
        return [OrderStatusEnum::Open];
    }

    protected function apply(): void
    {
        $attributes = array_filter([
            'customer_id' => $this->getStringDifferenceOrNull($this->target->customer_id, $this->validated['customer_id'] ?? null),
            'billing_address_id' => $this->getStringDifferenceOrNull($this->target->billing_address_id, $this->validated['billing_address_id'] ?? null),
            'shipping_address_id' => $this->getStringDifferenceOrNull($this->target->shipping_address_id, $this->validated['shipping_address_id'] ?? null),
            'customer_note' => $this->getStringDifferenceOrNull($this->target->customer_note, $this->validated['customer_note'] ?? null),
        ]);

        if (!empty($attributes)) {
            $this->target->fill($attributes);
            $this->target->load('customer');
            $this->target->load('billingAddress');
            $this->target->load('shippingAddress');

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
