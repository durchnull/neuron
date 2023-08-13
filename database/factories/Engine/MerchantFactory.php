<?php

namespace Database\Factories\Engine;

use App\Generators\TokenGenerator;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Engine\Merchant>
 */
class MerchantFactory extends Factory
{
    /**
     * @return array<string, mixed>
     * @throws Exception
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'token' => TokenGenerator::make()->generate(),
        ];
    }
}
