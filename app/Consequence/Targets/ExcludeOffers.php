<?php

namespace App\Consequence\Targets;

class ExcludeOffers extends Target
{
    public static function make(): array
    {
        return [self::id()];
    }
}
