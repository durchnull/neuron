<?php

namespace App\Condition\Presets;

use App\Condition\Comparison;
use App\Condition\ComparisonTypeEnum;
use App\Condition\Condition;
use App\Condition\ConditionCollection;
use App\Condition\Property;
use App\Condition\PropertyTypeEnum;
use App\Condition\Value;
use Exception;

class OrderValueIsGreaterOrEqualToAmount
{
    public static function name(string $amount = null): string
    {
        $amount = $amount ?? 'amount';

        return "Order value is greater or equal to $amount";
    }

    /**
     * @throws Exception
     */
    public static function make(int $amount): ConditionCollection
    {
        return ConditionCollection::make()
            ->addCondition(
                Condition::make(
                    Property::make(PropertyTypeEnum::OrderItemsTotalAmount),
                    Comparison::make(ComparisonTypeEnum::GreaterEquals),
                    Value::make($amount)
                )
            );
    }
}
