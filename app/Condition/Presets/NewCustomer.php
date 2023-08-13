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

class NewCustomer
{
    public static function name(): string
    {
        return 'New customer';
    }

    /**
     * @throws Exception
     */
    public static function make(): ConditionCollection
    {
        return ConditionCollection::make()
            ->addCondition(
                Condition::make(
                    Property::make(PropertyTypeEnum::CustomerIsNew),
                    Comparison::make(ComparisonTypeEnum::Equals),
                    Value::make(true)
                )
            );
    }
}
