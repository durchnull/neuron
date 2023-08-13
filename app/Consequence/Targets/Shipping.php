<?php

namespace App\Consequence\Targets;

class Shipping extends Target
{
    public static function make(): array
    {
        return [self::id()];
    }
}
