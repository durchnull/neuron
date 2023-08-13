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

class ProductIdNotAllowedForCountry
{
    public static function name(string $productName = null, string $countryCode = null): string
    {
        $productName = $productName ?? 'Product';

        return "$productName is not allowed for country $countryCode";
    }

    /**
     * @throws Exception
     */
    public static function make(string $productId, string $countryCode): ConditionCollection
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
                    Property::make(PropertyTypeEnum::ShippingCountryCode),
                    Comparison::make(ComparisonTypeEnum::Equals),
                    Value::make($countryCode)
                )
            );
    }
}
