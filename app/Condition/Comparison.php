<?php

namespace App\Condition;

use Illuminate\Contracts\Support\Arrayable;

class Comparison implements Arrayable
{
    public function __construct(protected ComparisonTypeEnum $comparisonType)
    {
    }

    public static function make(ComparisonTypeEnum $comparisonType): Comparison
    {
        return new static($comparisonType);
    }

    public function getType(): ComparisonTypeEnum
    {
        return $this->comparisonType;
    }

    public function toArray(): mixed
    {
        return $this->comparisonType->value;
    }
}
