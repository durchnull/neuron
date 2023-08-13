<?php

namespace App\Generators;

use App\Enums\Generator\StringPattern;

class TokenGenerator extends StringGenerator
{
    public static function make(): TokenGenerator
    {
        return new static(StringPattern::TwentyAlphaNumericDashesUpper);
    }
}
