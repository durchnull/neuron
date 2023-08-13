<?php

namespace App\Actions\Engine\Order;

use App\Enums\Order\OrderStatusEnum;
use App\Facades\Order;
use App\Facades\Rule;
use App\Models\Engine\CartRule;

class OrderRemoveCartRuleAction extends OrderAction
{
    public static function rules(): array
    {
        return [
            'order_id' => 'required|uuid|exists:orders,id',
            'cart_rule_id' => 'required|exists:cart_rules,id'
        ];
    }

    protected function gate(array $attributes): void
    {
        parent::gate($attributes);
    }

    public static function afterState(): array
    {
        return [OrderStatusEnum::Open];
    }

    protected function apply(): void
    {
        $cartRule = CartRule::find($this->validated['cart_rule_id']);

        $this->target->cartRules()->detach($cartRule);
        $this->target->setRelation('cartRules', collect()->concat($this->target->cartRules->where('id', '!=', $cartRule->id)));

        $this->target = Rule::apply($this->target);

        $this->target->update(
            array_merge(Order::getTotals($this->target), [
                'version' => $this->target->version + 1
            ])
        );

        $this->target = Order::updatePayment($this->target);
    }
}
