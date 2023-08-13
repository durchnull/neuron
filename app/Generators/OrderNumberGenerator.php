<?php

namespace App\Generators;

use App\Enums\Generator\StringPattern;

class OrderNumberGenerator extends StringGenerator
{
    public static function make(): OrderNumberGenerator
    {
        return new static(StringPattern::YearDashesNineAlphaNumericUpper);
    }
}
