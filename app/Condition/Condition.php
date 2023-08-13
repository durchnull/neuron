<?php

namespace App\Condition;

use Illuminate\Contracts\Support\Arrayable;

class Condition implements Arrayable
{
    public function __construct(
        protected Property $property,
        protected Comparison $comparison,
        protected Value $value,
    ) {}

    public static function make(
        Property $property,
        Comparison $comparison,
        Value $value,
    ): Condition
    {
        return new static(
            $property,
            $comparison,
            $value
        );
    }

    public function getProperty(): Property
    {
        return $this->property;
    }

    public function getComparison(): Comparison
    {
        return $this->comparison;
    }

    public function getValue(): Value
    {
        return $this->value;
    }

    public static function fromArray(array $data): Condition
    {
        return Condition::make(
            Property::make(
                PropertyTypeEnum::from($data[0]),
            ),
            Comparison::make(
                ComparisonTypeEnum::from($data[1])
            ),
            Value::make($data[2])
        );
    }

    public function toArray(): mixed
    {
        return [
            $this->property->toArray(),
            $this->comparison->toArray(),
            $this->value->toArray(),
        ];
    }
}
