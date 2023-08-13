<?php

namespace Database\Factories\Engine;

use App\Condition\Comparison;
use App\Condition\ComparisonTypeEnum;
use App\Condition\Condition;
use App\Condition\ConditionCollection;
use App\Condition\Operator;
use App\Condition\OperatorTypeEnum;
use App\Condition\Property;
use App\Condition\PropertyTypeEnum;
use App\Condition\Value;
use App\Models\Engine\SalesChannel;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Engine\Condition>
 */
class ConditionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws Exception
     */
    public function definition(): array
    {
        return [
            'sales_channel_id' => SalesChannel::factory(),
            'name' => $this->faker->word,
            'collection' => $this->randomConditionCollection(),
        ];
    }

    /**
     * @throws Exception
     */
    private function randomConditionCollection(): ConditionCollection
    {
        $conditionCollection = ConditionCollection::make();

        $rangeEnd = $this->faker->randomElement([
            0,
            1,
            3,
            5
        ]);

        foreach (range(0, $rangeEnd) as $count) {
            $conditions = $conditionCollection->getElements();

            if (empty($conditions)) {
                $conditionCollection->addCondition(
                    $this->randomCondition()
                );
            } else {
                $last = end($conditions);

                if ($last instanceof Operator) {
                    if ($this->faker->boolean) {
                        $conditionCollection->addCondition(
                            $this->randomCondition()
                        );
                    } else {
                        $conditionCollection->addConditionCollection(
                            $this->randomConditionCollection(1)
                        );
                    }
                } elseif ($count !== $rangeEnd) {
                    $conditionCollection->addOperator(
                        $this->randomOperator()
                    );
                }
            }
        }

        return $conditionCollection;
    }


    private function randomCondition(): Condition
    {
        return Condition::make(
            $this->randomProperty(),
            $this->randomComparison(),
            $this->randomValue(),
        );
    }

    private function randomProperty(): Property
    {
        return Property::make(
            $this->faker->randomElement(PropertyTypeEnum::cases())
        );
    }

    private function randomComparison(): Comparison
    {
        return Comparison::make(
            $this->faker->randomElement(ComparisonTypeEnum::cases())
        );
    }

    private function randomValue(): Value
    {
        return Value::make(
            $this->faker->randomElement([
                $this->faker->numberBetween(0, 1000),
                $this->faker->word
            ])
        );
    }

    private function randomOperator(): Operator
    {
        return Operator::make(
            $this->faker->randomElement(OperatorTypeEnum::cases())
        );
    }
}
