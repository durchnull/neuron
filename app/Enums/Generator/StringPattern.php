<?php

namespace App\Enums\Generator;

enum StringPattern: string
{
    case NineAlphaNumericLower = 'xxxxxxxxx';
    case NineAlphaNumericUpper = 'XXXXXXXXX';
    case YearDashesNineAlphaNumericUpper = 'YYYY-XXX-XXX-XXX';
    case TwentyAlphaNumericDashesUpper = 'XXXX-XXXX-XXXX-XXXX-XXXX';
}
