<?php

namespace Database\Factories\Integration\Marketing;

use App\Generators\CouponCodeGenerator;
use App\Models\Engine\Merchant;
use App\Models\Engine\Product;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Integration\Marketing\Klicktipp>
 */
class KlicktippFactory extends Factory
{
    /**
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function definition(): array
    {
        return [
            'sales_channel_id' => SalesChannel::factory(),
            'enabled' => $this->faker->boolean,
            'distribute_order' => $this->faker->boolean,
            'name' => 'Klicktipp',
            'user_name' => Str::slug($this->faker->userName),
            'developer_key' => $this->faker->lexify('????????????????????????????????????????????????????????????????'),
            'customer_key' =>  $this->faker->lexify('??????????????????????????????????????????????????????'),
            'service' => 'https://api.klicktipp.com',
            'tag_prefix' => 'PRE_',
            'tags' => [
                (string)$this->faker->numberBetween(1000000, 10000000)
            ],
            'tags_coupons' => [
                (string)$this->faker->numberBetween(1000000, 10000000) => [
                    CouponCodeGenerator::make()->generate(),
                    CouponCodeGenerator::make()->generate(),
                    CouponCodeGenerator::make()->generate(),
                ]
            ],
            'tags_periods' => [
                (string)$this->faker->numberBetween(1000000, 10000000) => [
                    'begin_at' => now(),
                    'end_at' => now()->addDay(),
                ]
            ],
            'tags_new_customer' => [
                (string)$this->faker->numberBetween(1000000, 10000000)
            ],
            'tags_products' => [
                (string)$this->faker->numberBetween(1000000, 10000000) => [
                    Str::uuid()->toString(),
                    Str::uuid()->toString(),
                    Str::uuid()->toString(),
                ]
            ],
        ];
    }
}
