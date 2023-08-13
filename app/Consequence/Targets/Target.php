<?php

namespace App\Consequence\Targets;

use Illuminate\Support\Str;

abstract class Target
{
    public static function id(): string
    {
        return Str::kebab(class_basename(get_called_class()));
    }
}
