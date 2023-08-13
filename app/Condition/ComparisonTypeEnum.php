<?php

namespace App\Condition;

enum ComparisonTypeEnum: string
{
    case Equals = '=';
    case NotEquals = '!=';
    case Greater = '>';
    case Lesser = '<';
    case GreaterEquals = '>=';
    case LesserEquals = '<=';
    case ContainsOne = 'in';
    case ContainsAll = 'ina';
    case NotContains = '!in';
    case NotContainsAll = '!ina';
}
