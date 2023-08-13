<?php

namespace App\Consequence;

use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Arrayable;

abstract class Consequence implements Arrayable
{
    abstract public function toArray(): mixed;

    public static function getType(): string
    {
        return Str::kebab(class_basename(get_called_class()));
    }
}
