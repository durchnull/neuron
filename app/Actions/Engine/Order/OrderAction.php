<?php

namespace App\Actions\Engine\Order;

use App\Actions\Action;
use App\Enums\Order\PolicyReasonEnum;
use App\Enums\TriggerEnum;
use App\Facades\Rule;
use App\Models\Engine\Order;
use Illuminate\Support\Facades\Log;

abstract class OrderAction extends Action
{
    public function __construct(Order $target, array $attributes, TriggerEnum $trigger)
    {
        parent::__construct($target, $attributes, $trigger);

        if (!\App\Facades\Order::can($this)) {
            Log::channel('order')->info('Order flow constraint: ' . class_basename(get_called_class()) . ' [' . $target->status->value . ']');
            $this->addPolicy(PolicyReasonEnum::OrderFlowConstraint);
        }
    }

    final public static function targetClass(): string
    {
        return Order::class;
    }

    protected function gate(array $attributes): void
    {
        Rule::validateAction($this);
    }

    abstract public static function afterState(): array;
}
