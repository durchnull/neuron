<?php

namespace App\Contracts\Engine;

use App\Actions\Engine\Order\OrderAction;
use App\Condition\ConditionCollection;
use App\Models\Engine\Order;
use Illuminate\Support\Collection;

interface RuleServiceContract
{

    public function validateAction(OrderAction $action): void;

    public function validate(ConditionCollection $collection, Order $order, OrderAction $action = null): bool;

    public function getSortedRules(Order $order): Collection;

    public function apply(Order $order): Order;

    public function allowedActionRuleClasses(): array;
}
