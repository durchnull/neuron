<?php

namespace Database\Factories\Engine;

use App\Enums\Product\ProductTypeEnum;
use App\Models\Integration\Inventory\Billbee;
use App\Models\Integration\Inventory\Inventory;
use App\Models\Integration\Inventory\NeuronInventory;
use App\Models\Integration\Inventory\Weclapp;
use App\Models\Engine\Product;
use App\Models\Engine\SalesChannel;
use App\Product\Configuration\BundleConfiguration;
use App\Product\Configuration\BundleConfigurationGroup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Engine\Product>
 */
class ProductFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /** @var Inventory $inventory */
        $inventory = ($this->faker->randomElement([
            NeuronInventory::class,
            Billbee::class,
            Weclapp::class
        ]))::factory()->create();

        $name = $this->faker->randomElement([
            'Bonbon',
            'Coffee',
            'Cupcake',
            'Potion',
            'Power Bundle'
        ]);

        $type = ProductTypeEnum::Product;
        $configuration = null;

        if (Str::contains($name, 'Bundle')) {
            $type = ProductTypeEnum::Bundle;
            $configuration = $this->fakerConfiguration();
        }

        return [
            'sales_channel_id' => SalesChannel::factory(),
            'inventoryable_id' => $inventory->id,
            'inventoryable_type' => get_class($inventory),
            'inventory_id' => $this->faker->boolean ? $this->faker->uuid : null,
            'enabled' => $this->faker->boolean,
            'name' => $name,
            'type' => $type,
            'sku' => 'product-' . $this->faker->word . '-' . $this->faker->numberBetween(1, 100),
            'ean' => $this->faker->boolean ? $this->faker->numberBetween(10000000, 99999999) : null,
            'net_price' => $this->faker->numberBetween(1, 1000),
            'gross_price' => $this->faker->numberBetween(1, 1000),
            'configuration' => $configuration,
            'url' => $this->faker->url,
            'image_url' => $this->faker->imageUrl(400, 400, 'product', true, null, false, 'jpg'),
            'version' => $this->faker->numberBetween(1, 10),
        ];
    }

    protected function fakerConfiguration(): array
    {
        $products = [];

        foreach (range(1, 4) as $index1) {
            $products[] = Product::factory()->product()->create();
        }

        $configuration = BundleConfiguration::make();

        foreach (range(2, 4) as $index2) {
            $group = BundleConfigurationGroup::make();

            foreach (range(1, 4) as $index3) {
                $group->addProduct($this->faker->randomElement($products));
            }

            $configuration->addGroup($group);
        }

        return $configuration->toArray();
    }

    public function bundle(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => ProductTypeEnum::Bundle->value,
                'configuration' => $this->fakerConfiguration()
            ];
        });
    }
    public function product(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => ProductTypeEnum::Product->value,
                'configuration' => null
            ];
        });
    }
}
