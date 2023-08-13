<?php

namespace App\Condition;

enum OperatorTypeEnum: string
{
    case And = 'and';
    case Or = 'or';
}
