<?php

namespace Database\Factories\Engine;

use App\Consequence\AddItem;
use App\Consequence\Consequence;
use App\Consequence\ConsequenceCollection;
use App\Consequence\Discount;
use App\Consequence\Targets\ProductAll;
use App\Consequence\Targets\ProductIds;
use App\Consequence\Targets\Shipping;
use App\Models\Engine\Condition;
use App\Models\Engine\Product;
use App\Models\Engine\SalesChannel;
use App\Models\Engine\Stock;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Engine\Rule>
 */
class RuleFactory extends Factory
{
    /**
     * @return array<string, mixed>
     * @throws Exception
     */
    public function definition(): array
    {
        return [
            'sales_channel_id' => SalesChannel::factory(),
            'condition_id' => Condition::factory(),
            'name' => $this->faker->sentence,
            'consequences' => $this->randomConsequenceCollection(),
            'position' => $this->faker->numberBetween(0, 10),
        ];
    }

    public function enabled(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'enabled' => true,
            ];
        });
    }

    public function disabled(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'enabled' => false,
            ];
        });
    }

    private function randomConsequenceCollection(int $max = 2): ConsequenceCollection
    {
        $consequenceCollection = ConsequenceCollection::make();

        foreach (range(0, $this->faker->numberBetween(0, $max)) as $count) {
            $consequenceCollection->addConsequence(
                $this->randomConsequence()
            );
        }

        return $consequenceCollection;
    }

    private function randomConsequence(): Consequence
    {
        return $this->faker->randomElement([
            $this->randomConsequenceAddItem(),
            $this->randomConsequenceDiscount(),
        ]);
    }

    private function randomConsequenceAddItem(int $max = 3): AddItem
    {
        return AddItem::make(
            Str::uuid()->toString(),
            Product::factory()
                ->has(Stock::factory())
                ->create()->id,
            $this->faker->numberBetween(1, $max),
            []
        );
    }

    private function randomConsequenceDiscount(int $max = 3000): Discount
    {
        $percentage = $this->faker->boolean;

        return Discount::make(
            $this->faker->numberBetween(1, $percentage ? 100 : $max),
            $percentage,
            $this->randomTargets()
        );
    }

    private function randomTargets(): array
    {
        return $this->faker->randomElements([
            ProductAll::make(),
            ProductIds::make([
                Product::factory()->create()->id
            ]),
            Shipping::make()
        ], $this->faker->numberBetween(1, 3), false);
    }
}
