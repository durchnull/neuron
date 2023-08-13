<?php

namespace App\Generators;

use App\Enums\Generator\StringPattern;

class CouponCodeGenerator extends StringGenerator
{
    public static function make(): CouponCodeGenerator
    {
        return new static(StringPattern::NineAlphaNumericUpper);
    }
}
