<?php

namespace App\Facades;

use App\Actions\Engine\Order\OrderAction;
use App\Condition\ConditionCollection;
use App\Contracts\Engine\OrderServiceContract;
use App\Models\Engine\Condition;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Models\Engine\Order new()
 * @method static \App\Models\Engine\Order get()
 * @method static bool open()
 * @method static OrderServiceContract set(\App\Models\Engine\Order $order)
 * @method static OrderServiceContract setById(string $id)
 * @method static bool can(OrderAction $action)
 * @method static \App\Models\Engine\Order update(\App\Models\Engine\Order $order)
 * @method static \App\Models\Engine\Order updateStatus(\App\Models\Engine\Order $order)
 * @method static \App\Models\Engine\Order updateItems(\App\Models\Engine\Order $order)
 * @method static \App\Models\Engine\Order updatePayment(\App\Models\Engine\Order $order)
 * @method static \App\Models\Engine\Order updateCartRules(\App\Models\Engine\Order $order)
 * @method static array getTotals(\App\Models\Engine\Order $order)
 * @method static Collection getSortedRules(\App\Models\Engine\Order $order)
 * @method static bool validateConditionCollection(ConditionCollection $conditionCollection, \App\Models\Engine\Order $order)
 * @method static bool validateCondition(Condition $condition, \App\Models\Engine\Order $order)
 * @method static string generateOrderNumber()
 *
 * @see OrderService
 */
class Order extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'order';
    }
}
