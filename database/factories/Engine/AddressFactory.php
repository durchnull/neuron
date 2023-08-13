<?php

namespace Database\Factories\Engine;

use App\Enums\Address\SalutationEnum;
use App\Models\Engine\Customer;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Engine\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sales_channel_id' => SalesChannel::factory(),
            'customer_id' => Customer::factory(),
            'primary' => $this->faker->boolean,
            'company' => $this->faker->boolean ? $this->faker->company : null,
            'salutation' => $this->faker->randomElement(array_column(SalutationEnum::cases(), 'value')),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'street' => $this->faker->streetName,
            'number' => $this->faker->streetSuffix,
            'additional' => $this->faker->boolean ? $this->faker->sentence : null,
            'postal_code' => $this->faker->postcode,
            'city' => $this->faker->city,
            'country_code' => $this->faker->countryCode,
        ];
    }
}
