<?php

namespace App\Condition\Presets;

use App\Condition\Comparison;
use App\Condition\ComparisonTypeEnum;
use App\Condition\Condition;
use App\Condition\ConditionCollection;
use App\Condition\Operator;
use App\Condition\OperatorTypeEnum;
use App\Condition\Property;
use App\Condition\PropertyTypeEnum;
use App\Condition\Value;
use Exception;

class MaxActionProductQuantity
{
    public static function name(string $productName = null, int $quantity = null): string
    {
        $productName = $productName ?? 'product';

        return "Maximum $productName quantity $quantity";
    }

    /**
     * @throws Exception
     */
    public static function make(string $productId, int $quantity): ConditionCollection
    {
        return ConditionCollection::make()
            ->addCondition(
                Condition::make(
                    Property::make(PropertyTypeEnum::ActionProductId),
                    Comparison::make(ComparisonTypeEnum::Equals),
                    Value::make($productId)
                )
            )
            ->addOperator(Operator::make(OperatorTypeEnum::And))
            ->addCondition(
                Condition::make(
                    Property::make(PropertyTypeEnum::OrderProductQuantity),
                    Comparison::make(ComparisonTypeEnum::Greater),
                    Value::make([$productId, $quantity])
                )
            );
    }
}
