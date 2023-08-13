<?php

namespace App\Actions\Engine\Order;

use App\Enums\Integration\IntegrationTypeEnum;
use App\Enums\Order\OrderStatusEnum;
use App\Facades\Integrations;
use App\Facades\Order;
use App\Facades\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Enum;

class OrderUpdateStatusAction extends OrderAction
{
    public static function rules(): array
    {
        return [
            'order_id' => 'required|uuid|exists:orders,id',
            'status' => ['nullable', new Enum(OrderStatusEnum::class)]
        ];
    }

    public static function afterState(): array
    {
        return [
            // @todo [action] all?
            OrderStatusEnum::Open,
            OrderStatusEnum::Accepted,
            OrderStatusEnum::Confirmed
        ];
    }

    protected function apply(): void
    {
        $attributes = array_filter([
           'status' => $this->getStringDifferenceOrNull($this->target->status->value, $this->validated['status']->value ?? null)
        ]);

        if (!empty($attributes)) {
            $acceptedEvent = null;
            $confirmedEvent = null;
            $canceledEvent = null;

            Log::channel('order')->info('OrderUpdateStatusAction: Update status from [' .  $this->target->status->value . '] to [' . $this->validated['status']->value . ']');

            if (isset($this->validated['status']) &&
                $this->target->status === OrderStatusEnum::Placing &&
                $this->validated['status'] === OrderStatusEnum::Accepted
            ) {
                $acceptedEvent = true;
            }

            if (isset($this->validated['status']) &&
                in_array($this->target->status, [OrderStatusEnum::Placing, OrderStatusEnum::Accepted]) &&
                $this->validated['status'] === OrderStatusEnum::Confirmed
            ) {
                $confirmedEvent = true;
            }

            if (isset($this->validated['status']) &&
                $this->validated['status'] === OrderStatusEnum::Canceled
            ) {
                $canceledEvent = true;
            }

            $this->target->fill($attributes);

            if ($this->validated['status'] === OrderStatusEnum::Open->value) {
                $this->target = Rule::apply($this->target);
            }

            $this->target->update(
                array_merge(Order::getTotals($this->target), [
                    'version' => $this->target->version + 1,
                ], $attributes)
            );

            if ($acceptedEvent) {
                Log::channel('order')->info('Accepted order');
                Integrations::distributeOrder($this->target, [IntegrationTypeEnum::Mail]);
                $this->target = Order::updateStatus($this->target);
                $this->target = Order::updatePayment($this->target);
            } elseif ($confirmedEvent) {
                Log::channel('order')->info('Confirmed order');
                Integrations::distributeOrder($this->target, [IntegrationTypeEnum::Inventory]);
                $this->target = Order::updateStatus($this->target);
                $this->target = Order::updatePayment($this->target);
            } elseif ($canceledEvent) {
                Log::channel('order')->info('Canceled order');
                Integrations::cancelOrder($this->target, [IntegrationTypeEnum::Inventory]);
                // @todo
            }
        }
    }
}
