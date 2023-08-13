<?php

namespace App\Actions\Engine\Order;

use App\Enums\Order\OrderStatusEnum;
use App\Facades\Order;
use App\Facades\Rule;

class OrderCreateAction extends OrderAction
{
    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid',
            'shipping_id' => 'required|uuid|exists:shippings,id,enabled,1',
            'payment_id' => 'required|uuid|exists:payments,id,enabled,1',
        ];
    }

    protected function gate(array $attributes): void
    {
        parent::gate($attributes);
    }

    public static function afterState(): array
    {
        return [
            OrderStatusEnum::Open
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated);

        $this->target->load('shipping');
        $this->target->load('payment');

        $this->target->shipping_amount = $this->target->shipping->net_price;
        $this->target->shipping_discount_amount = 0;

        $this->target = Rule::apply($this->target);

        $this->target->fill(array_merge(Order::getTotals($this->target), [
            'order_number' => Order::generateOrderNumber()
        ]))->save();

        $this->target = Order::updatePayment($this->target);
        $this->target = Order::updateCartRules($this->target);
    }
}
