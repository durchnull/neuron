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

// @todo [test] [implementation]
class CustomerEmail
{
    public static function name(string $email): string
    {
        return 'Customer email is ' . $email;
    }

    /**
     * @throws Exception
     */
    public static function make(string $email): ConditionCollection
    {
        return ConditionCollection::make()
            ->addCondition(
                Condition::make(
                    Property::make(PropertyTypeEnum::CustomerEmail),
                    Comparison::make(ComparisonTypeEnum::Equals),
                    Value::make($email)
                )
            );
    }
}
