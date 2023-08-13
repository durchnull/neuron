<?php

namespace App\Condition;

use Illuminate\Contracts\Support\Arrayable;

class Operator implements Arrayable
{
    public function __construct(protected OperatorTypeEnum $operatorType)
    {
    }

    public static function make(OperatorTypeEnum $operatorType): Operator
    {
        return new static($operatorType);
    }

    public function getType(): OperatorTypeEnum
    {
        return $this->operatorType;
    }

    public static function fromArray(string $data): Operator
    {
        return self::make(
            OperatorTypeEnum::from($data)
        );
    }

    public function toArray(): mixed
    {
        return $this->operatorType->value;
    }
}
