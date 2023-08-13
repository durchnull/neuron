<?php

namespace App\Consequence\Targets;

class ProductAll extends Target
{
    public static function make(): array
    {
        return [self::id()];
    }
}
