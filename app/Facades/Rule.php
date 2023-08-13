<?php

namespace App\Facades;

use App\Actions\Action;
use App\Condition\ConditionCollection;
use App\Models\Engine\Order;
use App\Services\Engine\RuleService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void validateAction(Action $action)
 * @method static bool validate(ConditionCollection $condition, Order $order)
 * @method static Order apply(Order $order)
 * @method static Collection getSortedRules(Order $order):
 * @method static array allowedActionRuleClasses():
 *
 * @see RuleService
 */
class Rule extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rule';
    }
}
