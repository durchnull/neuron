<?php

namespace App\Actions\Engine\Order;

use App\Enums\Order\OrderStatusEnum;
use App\Facades\Order;
use App\Facades\Rule;
use App\Facades\Transaction;

class OrderUpdatePaymentAction extends OrderAction
{
    public static function rules(): array
    {
        return [
            'order_id' => 'required|uuid|exists:orders,id',
            'payment_id' => 'nullable|uuid|exists:payments,id,enabled,1',
        ];
    }

    public static function afterState(): array
    {
        return [OrderStatusEnum::Open];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated);

        if ($this->target->isDirty()) {
            $this->target->load('payment');
            $this->target = Rule::apply($this->target);

            $this->target->update(
                array_merge(Order::getTotals($this->target), [
                    'version' => $this->target->version + 1,
                ], $this->validated)
            );

            // @todo [test]
            Transaction::cancel($this->target);
        }
    }
}
