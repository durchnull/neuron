<?php

namespace App\Condition;

use Illuminate\Contracts\Support\Arrayable;

class Value implements Arrayable
{
    public function __construct(protected mixed $value)
    {
    }

    public static function make(mixed $value): Value
    {
        return new static($value);
    }

    public function get(): mixed
    {
        return $this->value;
    }

    public function toArray(): mixed
    {
        return $this->value;
    }
}
