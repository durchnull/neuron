<?php

namespace App\Condition;

use Illuminate\Contracts\Support\Arrayable;

class Property implements Arrayable
{
    public function __construct(protected PropertyTypeEnum $propertyType)
    {
    }

    public static function make(PropertyTypeEnum $propertyType): Property
    {
        return new static($propertyType);
    }

    public function getType(): PropertyTypeEnum
    {
        return $this->propertyType;
    }

    public function toArray(): mixed
    {
        return $this->propertyType->value;
    }
}
