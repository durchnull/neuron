<?php

namespace App\Condition;

use Exception;
use Illuminate\Contracts\Support\Arrayable;

class ConditionCollection implements Arrayable
{
    protected array $elements;

    public function __construct()
    {
        $this->elements = [];
    }

    public static function make(): ConditionCollection
    {
        return new static();
    }

    /**
     * @throws Exception
     */
    public function addCondition(Condition $condition): ConditionCollection
    {
        if (empty($this->elements) || end($this->elements) instanceof Operator) {
            $this->elements[] = $condition;
        } else {
            throw new Exception('Add operator before condition');
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function addConditionCollection(ConditionCollection $conditionCollection): ConditionCollection
    {
        if (empty($this->elements) || end($this->elements) instanceof Operator) {
            $this->elements[] = $conditionCollection;
        } else {
            throw new Exception('Add operator before conditions');
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function addOperator(Operator $operator): ConditionCollection
    {
        if (!empty($this->elements) || end($this->elements) instanceof Condition) {
            $this->elements[] = $operator;
        } else {
            throw new Exception('Add condition before operator');
        }

        return $this;
    }

    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @throws Exception
     */
    public static function fromArray(array $data): ConditionCollection
    {
        $conditionCollection = ConditionCollection::make();

        foreach ($data as $_data) {
            if (is_string($_data) && in_array($_data, array_map(fn(OperatorTypeEnum $operatorType) => $operatorType->value, OperatorTypeEnum::cases()))) {
                $conditionCollection->addOperator(
                    Operator::fromArray($_data)
                );
            } elseif (is_array($_data)) {
                if (count($_data) === 3 && !is_array($_data[0])) {
                    $conditionCollection->addCondition(
                        Condition::fromArray($_data)
                    );
                } else {
                    $conditionCollection->addConditionCollection(
                        self::fromArray($_data)
                    );
                }
            }
        }

        return $conditionCollection;
    }

    public function toArray(): mixed
    {
        return array_map(
            fn($item) => $item->toArray(),
            $this->elements
        );
    }
}
