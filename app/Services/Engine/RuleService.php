<?php

namespace App\Services\Engine;

use App\Actions\Engine\Order\OrderAction;
use App\Actions\Engine\Order\OrderAddItemAction;
use App\Actions\Engine\Order\OrderRemoveItemAction;
use App\Actions\Engine\Order\OrderUpdateItemAction;
use App\Actions\Engine\Order\OrderUpdateItemQuantityAction;
use App\Condition\ComparisonTypeEnum;
use App\Condition\Condition;
use App\Condition\ConditionCollection;
use App\Condition\Operator;
use App\Condition\OperatorTypeEnum;
use App\Condition\PropertyTypeEnum;
use App\Consequence\AddItem;
use App\Consequence\Consequence;
use App\Consequence\ConsequenceCollection;
use App\Consequence\Credit;
use App\Consequence\Discount;
use App\Consequence\Targets\ItemReference;
use App\Consequence\Targets\ProductAll;
use App\Consequence\Targets\ProductIds;
use App\Consequence\Targets\Shipping;
use App\Contracts\Engine\OrderServiceContract;
use App\Contracts\Engine\RuleServiceContract;
use App\Contracts\Engine\SalesChannelContract;
use App\Contracts\Engine\StockServiceContract;
use App\Enums\Order\PolicyReasonEnum;
use App\Enums\TriggerEnum;
use App\Exceptions\Order\PolicyException;
use App\Models\Engine\ActionRule;
use App\Models\Engine\Customer;
use App\Models\Engine\Item;
use App\Models\Engine\Order;
use App\Models\Engine\Product;
use App\Models\Engine\Rule;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RuleService implements RuleServiceContract
{
    // <editor-fold desc="Header">

    public function __construct(
        protected SalesChannelContract $salesChannelService,
        protected OrderServiceContract $orderService,
        protected StockServiceContract $stockService,
        protected array $actionRuleClasses
    ) {
    }


    // </editor-fold>

    // <editor-fold desc="ActionRules">

    public function allowedActionRuleClasses(): array
    {
        return $this->actionRuleClasses;
    }

    // </editor-fold>

    // <editor-fold desc="Validation">

    /**
     * @throws Exception
     */
    public function validateAction(OrderAction $action): void
    {
        // @todo [cache]
        $actionRules = ActionRule::with('condition')
            ->where([
                'sales_channel_id' => $this->salesChannelService->id(),
                'action' => class_basename($action),
                'enabled' => true
            ])
            ->get();

        if ($actionRules->isNotEmpty()) {
            Log::channel('rules')->info('Validate action ' . class_basename($action));
        }

        /** @var ActionRule $actionRule */
        foreach ($actionRules as $actionRule) {
            Log::channel('rules')->info($actionRule->condition->name);

            if ($this->validate($actionRule->condition->collection, $action->target(), $action)) {
                Log::channel('rules')->info('Policy ActionRule');
                $action->addPolicy(PolicyReasonEnum::ActionRule);
            } else {
                Log::channel('rules')->info('Pass');
            }
        }
    }

    /**
     * @throws Exception
     */
    public function validate(
        ConditionCollection $collection,
        Order $order,
        OrderAction $action = null
    ): bool {
        $elements = $collection->getElements();

        if (empty($elements)) {
            return true;
        }

        $statement = [];

        foreach ($elements as $element) {
            if ($element instanceof Condition) {
                $statement[] = $this->validateCondition($element, $order, $action);
            } elseif ($element instanceof ConditionCollection) {
                $statement[] = $this->validate($element, $order);
            } elseif ($element instanceof Operator) {
                $statement[] = $element->getType()->value;
            }
        }

        $result = null;
        $operator = OperatorTypeEnum::And->value;

        Log::channel('rules')->info(json_encode($statement));

        foreach ($statement as $item) {
            if ($item === OperatorTypeEnum::And->value || $item === OperatorTypeEnum::Or->value) {
                $operator = $item;
            } else {
                $condition = (bool)$item;

                if ($operator === OperatorTypeEnum::And->value) {
                    $result = $result === null ? $condition : $result && $condition;
                } elseif ($operator === OperatorTypeEnum::Or->value) {
                    if ($result === true) {
                        return true; // @todo [test]
                    }
                    $result = $result === null ? $condition : $result || $condition;
                }
            }
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    protected function validateCondition(
        Condition $condition,
        Order $order,
        OrderAction $action = null
    ): bool {
        Log::channel('rules')->info('Validate condition ' . $condition->getProperty()->getType()->value);

        return match ($condition->getProperty()->getType()) {
            PropertyTypeEnum::DateTime => $this->validateConditionDatetime($condition),
            PropertyTypeEnum::OrderItemsTotalAmount => $this->validateConditionItemsTotalAmount($condition, $order),
            PropertyTypeEnum::OrderProductQuantity => $this->validateProductQuantity($condition, $order, $action),
            PropertyTypeEnum::OrderProductIds => $this->validateProductIds($condition, $order, $action),
            PropertyTypeEnum::ActionProductId => $this->validateActionProduct($condition, $order, $action),
            PropertyTypeEnum::CustomerIsNew => $this->validateNewCustomer($condition, $order),
            PropertyTypeEnum::CustomerEmail => $this->validateCustomerEmail($condition, $order),
            default => throw new Exception($condition->getProperty()->getType()->value . ' not implemented'),
        };
    }

    protected function validateConditionDatetime(Condition $condition): bool
    {
        $now = Carbon::now();
        $then = Carbon::parse($condition->getValue()->get());
        $operator = $condition->getComparison()->getType();

        if ($operator === ComparisonTypeEnum::Lesser) {
            return $now->isBefore($then);
        } elseif ($operator === ComparisonTypeEnum::LesserEquals) {
            return $now->isBefore($then) || $now === $then;
        } elseif ($operator === ComparisonTypeEnum::Equals) {
            return $now === $then;
        } elseif ($operator === ComparisonTypeEnum::Greater) {
            return $now->isAfter($then);
        } elseif ($operator === ComparisonTypeEnum::GreaterEquals) {
            return $now->isAfter($then) || $now === $then;
        }

        return false;
    }

    protected function validateConditionItemsTotalAmount(Condition $condition, Order $order): bool
    {
        $totals = $this->orderService->getTotals($order);
        $itemsTotalAmount = $totals['items_amount'] - $totals['items_discount_amount'];
        $operator = $condition->getComparison()->getType();
        $amount = $condition->getValue()->get();

        if ($operator === ComparisonTypeEnum::Lesser) {
            return $itemsTotalAmount < $amount;
        } elseif ($operator === ComparisonTypeEnum::LesserEquals) {
            return $itemsTotalAmount <= $amount;
        } elseif ($operator === ComparisonTypeEnum::Equals) {
            return $itemsTotalAmount === $amount;
        } elseif ($operator === ComparisonTypeEnum::Greater) {
            return $itemsTotalAmount > $amount;
        } elseif ($operator === ComparisonTypeEnum::GreaterEquals) {
            return $itemsTotalAmount >= $amount;
        }

        return false;
    }

    protected function validateProductQuantity(Condition $condition, Order $order, OrderAction $action): bool
    {
        $value = $condition->getValue()->get();
        $productId = $value[0];
        $maxQuantity = $value[1];

        $nextQuantity = null;

        $validated = $action->validated();

        if (get_class($action) === OrderAddItemAction::class) {
            $currentQuantity = $order->items
                ->filter(fn(Item $item) => $item->product_id === $productId)
                ->sum(fn(Item $item) => $item->quantity);

            $nextQuantity = $currentQuantity + $validated['quantity'];
            // @todo [test]
        } elseif (get_class($action) === OrderUpdateItemAction::class && is_numeric($validated['quantity'])) {
            $nextQuantity = $validated['quantity'];
            // @todo Both actions?
        } elseif (get_class($action) === OrderUpdateItemQuantityAction::class && is_numeric($validated['quantity'])) {
            $nextQuantity = $validated['quantity'];
        } else {
            return false;
        }

        $operator = $condition->getComparison()->getType();

        Log::channel('rules')->info($nextQuantity . ' ' . $operator->value . ' ' . $maxQuantity);

        if ($operator === ComparisonTypeEnum::Lesser) {
            return $nextQuantity < $maxQuantity;
        } elseif ($operator === ComparisonTypeEnum::LesserEquals) {
            return $nextQuantity <= $maxQuantity;
        } elseif ($operator === ComparisonTypeEnum::Equals) {
            return $nextQuantity === $maxQuantity;
        } elseif ($operator === ComparisonTypeEnum::Greater) {
            return $nextQuantity > $maxQuantity;
        } elseif ($operator === ComparisonTypeEnum::GreaterEquals) {
            return $nextQuantity >= $maxQuantity;
        }

        return false;
    }

    protected function validateProductIds(Condition $condition, Order $order, OrderAction $action = null): bool
    {
        // @todo
        return false;
    }

    protected function validateActionProduct(Condition $condition, Order $order, OrderAction $action): bool
    {
        $operator = $condition->getComparison()->getType();
        $value = $condition->getValue()->get();

        Log::channel('rules')->info(json_encode($action->validated()));

        $actionProductId = null;

        if (get_class($action) === OrderAddItemAction::class) {
            $actionProductId = $action->validated()['product_id'];
        } elseif (get_class($action) === OrderUpdateItemAction::class) {
            $actionProductId = $order->items->first(fn(Item $item) => $item->id === $action->validated()['order_item_id'])->product_id;
            //@todo both classes?
        } elseif (get_class($action) === OrderUpdateItemQuantityAction::class) {
            $actionProductId = $order->items->first(fn(Item $item) => $item->id === $action->validated()['order_item_id'])->product_id;
        } else {
            return false;
        }

        Log::channel('rules')->info($actionProductId . ' ' . json_encode($value));

        if ($operator === ComparisonTypeEnum::Equals) {
            return $actionProductId === $value;
        } elseif ($operator === ComparisonTypeEnum::NotEquals) {
            return $actionProductId !== $value;
        } elseif ($operator === ComparisonTypeEnum::ContainsOne) {
            return in_array($actionProductId, $value);
        } elseif ($operator === ComparisonTypeEnum::NotContains) {
            return !in_array($actionProductId, $value);
        }

        return false;
    }

    protected function validateNewCustomer(Condition $condition, Order $order): bool
    {
        if (!$order->customer instanceof Customer) {
            return false;
        }

        $operator = $condition->getComparison()->getType();
        $value = $condition->getValue()->get();

        Log::channel('rules')->info(($order->customer->new ? 'new' : 'old') . ' ' . $operator->value . ' ' . $value);

        if ($operator === ComparisonTypeEnum::Equals) {
            return $order->customer->new === $value;
        } elseif ($operator === ComparisonTypeEnum::NotEquals) {
            return $order->customer->new !== $value;
        }

        return false;
    }

    protected function validateCustomerEmail(Condition $condition, Order $order): bool
    {
        if (!$order->customer instanceof Customer) {
            return false;
        }

        return $order->customer->email === $condition->getValue()->get();
    }

    // </editor-fold>

    // <editor-fold desc="Rules">

    public function getSortedRules(Order $order): Collection
    {
        // @todo [cache]
        return $order->coupons
            ->pluck('rule')
            ->merge($order->cartRules->pluck('rule'))
            ->sortBy(fn(Rule $rule) => $rule->position);
    }

    /**
     * @param  Order  $order
     * @return Order
     * @throws Exception
     */
    public function apply(Order $order): Order
    {
        Log::channel('rules')->info('');
        Log::channel('rules')->info('APPLY BEGIN');
        Log::channel('rules')->info('Order items count: ' . $order->items->count());

        $order->items->each(function (Item $item) {
            if ($item->reference === null) {
                $item->discount_amount = 0;
            }
        });

        $order->shipping_discount_amount = 0;

        $references = $order->items
            ->pluck('reference')
            ->filter()
            ->toArray();

        $consequences = [];

        /** @var Rule $rule */
        foreach ($this->getSortedRules($order) as $index => $rule) {
            Log::channel('rules')->info('Rule #' . $index . ' ' . $rule->name);

            if ($this->validate($rule->condition->collection, $order)) {
                Log::channel('rules')->info('TRUE condition for rule ' . $rule->condition->name);
                $consequences[$rule->id] = $this->applyConsequences($rule->consequences, $order);
            } else {
                Log::channel('rules')->info('False condition for rule ' . $rule->condition->name);
            }
        }

        $consequenceReferences = [];

        foreach ($consequences as $ruleId => $_consequences) {
            foreach ($_consequences as $consequence) {
                if (isset($consequence['reference'])) {
                    $consequenceReferences[] = $consequence['reference'];
                }
            }
        }

        Log::channel('rules')->info('Current references: ' . json_encode($references));
        Log::channel('rules')->info('Rule references:    ' . json_encode($consequenceReferences));

        $removeReferences = array_diff($references, $consequenceReferences);

        Log::channel('rules')->info('Remove references:  ' . json_encode($removeReferences));

        $removeItemIds = $order->items
            ->filter(fn(Item $item) => in_array($item->reference, $removeReferences))
            ->map(fn(Item $item) => $item->id)
            ->toArray();

        foreach ($removeItemIds as $removeItemId) {
            Log::channel('rules')->info('Remove item id: ' . $removeItemId);

            $removeItemAction = new OrderRemoveItemAction(
                \App\Facades\Order::get(),
                [
                    'order_id' => $order->id,
                    'order_item_id' => $removeItemId,
                    'reference_lock' => false
                ],
                TriggerEnum::Rule
            );

            $removeItemAction->trigger();
            $order = $removeItemAction->target();
        }

        $order->items->each(
            fn(Item $item) => $item->isDirty(['discount_amount']) ? $item->saveQuietly() : null
        );

        if ($order->isDirty(['shipping_discount_amount'])) {
            $order->saveQuietly();
        }

        Log::channel('rules')->info('Order items count: ' . $order->items->count());

        Log::channel('rules')->info('APPLY END');

        return $order;
    }

    // </editor-fold>

    // <editor-fold desc="Consequences">

    /**
     * @param  ConsequenceCollection  $consequenceCollection
     * @param  Order  $order
     * @return array
     * @throws ValidationException
     */
    protected function applyConsequences(ConsequenceCollection $consequenceCollection, Order &$order): array
    {
        $consequences = [];

        Log::channel('rules')->info('Consequences ' . count($consequenceCollection->getConsequences()));

        $hasCredit = collect($consequenceCollection->getConsequences())->contains(fn(Consequence $consequence) => $consequence instanceof Credit);

        Log::channel('rules')->info($hasCredit ? 'Contains credit' : '');

        /** @var Consequence $consequence */
        foreach ($consequenceCollection->getConsequences() as $consequence) {
            Log::channel('rules')->info('Consequence ' . get_class($consequence));
            switch (get_class($consequence)) {
                case Discount::class:
                    $consequences[] = $this->applyDiscountConsequence($consequence, $order);
                    break;
                case AddItem::class:
                    $consequences[] = $this->applyAddItemConsequence($consequence, $order);
                    break;
            }
        }

        return $consequences;
    }

    protected function applyDiscountConsequence(Discount $discount, Order &$order): array
    {
        $consequences = [
            'items' => [],
            'shipping' => false
        ];

        $targetedItemIds = $this->getTargetedItemIds($order->items, $discount->getTargets());

        foreach ($order->items as &$item) {
            if (in_array($item->id, $targetedItemIds)) {
                $consequences['items'][] = $item->id;
                $item->discount_amount = $this->getItemDiscount($item, $discount);
            }
        }

        if (collect($discount->getTargets())->contains(fn(array $target) => $target[0] === Shipping::id())) {
            $consequences['shipping'] = true;
            $order->shipping_discount_amount = $this->getShippingDiscount($order, $discount);
        }

        return [
            'discount' => $consequences
        ];
    }

    protected function getItemDiscount(Item $item, Discount $discount): int
    {
        Log::channel('rules')->info('Discount for item       ' . $item->id);

        $discountAmount = $discount->isPercentage()
            ? $item->total_amount * ($discount->getAmount() / 100.0)
            : $discount->getAmount();

        Log::channel('rules')->info('Item total amount       ' . $item->total_amount);
        Log::channel('rules')->info('Current discount amount ' . $item->discount_amount);
        Log::channel('rules')->info('Next discount amount    ' . min($item->total_amount, $item->discount_amount + $discountAmount));

        return min($item->total_amount, $item->discount_amount + $discountAmount);
    }

    protected function getShippingDiscount(Order $order, Discount $discount): int
    {
        return min(
            $order->shipping_amount,
            $order->shipping_discount_amount + (
            $discount->isPercentage()
                ? $order->shipping_amount * ($discount->getAmount() / 100.0)
                : $discount->getAmount()
            )
        );
    }

    protected function getTargetedItemIds(Collection $items, array $targets): array
    {
        $itemReferences = collect($targets)
            ->filter(fn(array $target) => $target[0] === ItemReference::id())
            ->map(fn(array $target) => $target[1])
            ->flatten()
            ->toArray();

        $allProducts = collect($targets)->contains(fn(array $target) => $target[0] === ProductAll::id());

        $productIds = collect($targets)->filter(fn(array $target) => $target[0] === ProductIds::id())
            ->map(fn(array $target) => $target[1])
            ->flatten()
            ->toArray();

        $itemIds = [];

        foreach ($items as $item) {
            if ($item->reference !== null) {
                if (in_array($item->reference, $itemReferences)) {
                    $itemIds[] = $item->id;
                }
            } elseif ($allProducts) {
                $itemIds[] = $item->id;
            } elseif (in_array($item->product_id, $productIds)) {
                $itemIds[] = $item->id;
            }
        }

        return $itemIds;
    }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    protected function applyAddItemConsequence(AddItem $addItem, Order &$order): array
    {
        $itemsContainReference = $order->items->contains(
            fn(Item $item) => $item->reference === $addItem->getReference()
        );

        // @todo [cache]
        $product = Product::find($addItem->getProductId());

        // @todo [test]
        if (!$itemsContainReference && $product->enabled && $this->stockService->has($addItem->getProductId(), $addItem->getQuantity())) {
            try {
                $action = new OrderAddItemAction(
                    $order,
                    [
                        'order_id' => $order->id,
                        'reference' => $addItem->getReference(),
                        'product_id' => $addItem->getProductId(),
                        'quantity' => $addItem->getQuantity(),
                        'configuration' => $addItem->getConfiguration(),
                    ],
                    TriggerEnum::Rule
                );

                $action->trigger();
                $order = $action->target();
            } catch (PolicyException $exception) {
                Log::channel('rules')->info($exception->getMessage());
            }
        }

        return [
            'reference' => $addItem->getReference()
        ];
    }

    // </editor-fold>
}
