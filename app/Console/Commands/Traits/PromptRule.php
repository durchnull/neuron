<?php

namespace App\Console\Commands\Traits;

use App\Condition\Comparison;
use App\Condition\ComparisonTypeEnum;
use App\Condition\Condition;
use App\Condition\ConditionCollection;
use App\Condition\Operator;
use App\Condition\OperatorTypeEnum;
use App\Condition\Property;
use App\Condition\PropertyTypeEnum;
use App\Condition\Value;
use App\Consequence\Consequence;
use App\Consequence\ConsequenceCollection;
use App\Consequence\Discount;
use App\Consequence\Targets\ItemReference;
use App\Consequence\Targets\ProductAll;
use App\Consequence\Targets\ProductIds;
use App\Consequence\Targets\Shipping;
use Exception;
use Illuminate\Support\Str;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

trait PromptRule
{

    /**
     * @throws Exception
     */
    public function promptConditionCollection(): ConditionCollection
    {
        $conditionCollection = ConditionCollection::make();
        $finish = false;

        while (empty($conditionCollection->getElements()) || !$finish) {
            $choices = [];

            $elements = $conditionCollection->getElements();

            if (empty($elements)) {
                $choices[] = 'Add condition';
                $choices[] = 'Add condition collection';
            } elseif (!end($elements) instanceof Operator) {
                $choices[] = 'Add operator';
                //$choices[] = 'Edit condition';
                //$choices[] = 'Edit conditions';
                $choices[] = 'Finish';
            } else {
                $choices[] = 'Add condition';
                $choices[] = 'Add condition collection';
                //$choices[] = 'Remove operator';
            }

            $default = null;

            if (in_array('Add condition', $choices)) {
                $default = 'Add condition';
            } elseif (in_array('Add operator', $choices)) {
                $default = 'Add operator';
            }

            $choice = select('Condition collection', $choices, $default);

            switch ($choice) {
                case 'Add condition':
                    $condition = $this->promptCondition();
                    $conditionCollection->addCondition($condition);
                    break;
                case 'Add condition collection':
                    $nestedConditionCollection = $this->promptConditionCollection();
                    $conditionCollection->addConditionCollection($nestedConditionCollection);
                    break;
                case 'Add operator':
                    $operator = $this->promptOperator();
                    $conditionCollection->addOperator($operator);
                    break;
                case 'Edit operator':
                case 'Edit condition';
                case 'Remove operator';
                    $this->error('Not implemented');
                    break;
                case 'Finish':
                    $finish = true;
                    break;
            }

            dump($conditionCollection->toArray());
        }

        return $conditionCollection;
    }

    public function promptCondition(): Condition
    {
        return Condition::make(
            $this->promptConditionProperty(),
            $this->promptConditionComparison(),
            $this->promptConditionValue(),
        );
    }

    public function promptConditionProperty(): Property
    {
        return Property::make(
            $this->promptConditionPropertyType()
        );
    }

    public function promptConditionPropertyType(): PropertyTypeEnum
    {
        return PropertyTypeEnum::from(
            select(
                'Type',
                array_map(
                    fn(PropertyTypeEnum $propertyTypeEnum) => $propertyTypeEnum->value,
                    PropertyTypeEnum::cases()
                )
            )
        );
    }

    public function promptConditionComparison(): Comparison
    {
        return Comparison::make(
            $this->promptConditionComparisonType()
        );
    }

    public function promptConditionComparisonType(): ComparisonTypeEnum
    {
        return ComparisonTypeEnum::from(
            select(
                'Type',
                array_map(
                    fn(ComparisonTypeEnum $comparisonType) => $comparisonType->value,
                    ComparisonTypeEnum::cases()
                )
            )
        );
    }

    public function promptConditionValue(): Value
    {
        return Value::make(
            text('Value')
        );
    }

    public function promptOperator(): Operator
    {
        return Operator::make(
            OperatorTypeEnum::from(
                select(
                    'Operator?',
                    array_map(
                        fn(OperatorTypeEnum $operatorType) => $operatorType->value,
                        OperatorTypeEnum::cases()
                    ), OperatorTypeEnum::And->value)
            )
        );
    }

    /**
     * @throws Exception
     */
    public function promptConsequenceCollection(): ConsequenceCollection
    {
        $consequenceCollection = ConsequenceCollection::make();

        $finish = false;

        while (empty($consequenceCollection->getConsequences()) || !$finish) {
            $choices = [
                'Add consequence',
                //'Edit consequence',
            ];

            if (!empty($consequenceCollection->getConsequences())) {
                $choices[] = 'Finish';
            }

            $choice = select('Consequences', $choices, 'Add consequence');

            switch ($choice) {
                case 'Add consequence':
                    $consequence = $this->promptConsequence();
                    $consequenceCollection->addConsequence($consequence);
                    break;
                case 'Edit consequence';
                    $this->error('Not implemented');
                    break;
                case 'Finish':
                    $finish = true;
                    break;
            }

            dump($consequenceCollection->toArray());
        }

        return $consequenceCollection;
    }

    /**
     * @throws Exception
     */
    public function promptConsequence(): Consequence
    {
        $class = select('Type', [
            \App\Consequence\AddItem::class,
            \App\Consequence\Discount::class,
        ]);

        return match ($class) {
            \App\Consequence\AddItem::class => \App\Consequence\AddItem::make(
                text('Reference?', Str::uuid()),
                $this->promptProduct()->id,
                $this->promptQuantity(),
                $this->promptConfiguration()
            ),
            Discount::class => Discount::make(
                $this->promptAmount('Amount'),
                $this->promptBool('Percentage', true),
                $this->promptDiscountTargets(),
            ),
            default => throw new Exception(),
        };
    }

    public function promptDiscountTargets(): array
    {
        $targets = [];
        $finish = false;

        while (empty($targets) && !$finish) {
            $choices = [
                'Add target',
                //'Edit consequence',
            ];

            if (count($targets) > 0) {
                $choices[] = 'Finish';
            }

            $choice = select('Targets', $choices, 'Add target');

            switch ($choice) {
                case 'Add target':
                    $target = $this->promptDiscountTarget();
                    $targets[] = $target;
                    break;
                case 'Edit target';
                    $this->error('Not implemented');
                    break;
                case 'Finish':
                    $finish = true;
                    break;
            }
        }

        return $targets;
    }

    public function promptDiscountTarget(): array
    {
        $choices = [
            ItemReference::id(),
            ProductAll::id(),
            ProductIds::id(),
            Shipping::id(),
        ];

        $choice = select('Target', $choices, ProductAll::id());

        switch ($choice) {
            case ItemReference::id():
                return ItemReference::make([
                    text('Reference', Str::uuid())
                ]);
            case ProductIds::id():
                $productIds = [];
                $finish = false;

                while (empty($productIds) && !$finish) {
                    $productIds[] = $this->promptProduct()->id;

                    if ($this->confirm('Finish?', true)) {
                        $finish = true;
                    }
                }

                return ProductIds::make($productIds);
            case Shipping::id():
                return Shipping::make();
            default:
            case ProductAll::id():
                return ProductAll::make();
        }
    }
}
