<?php

namespace Tests;

use App\Enums\Address\SalutationEnum;

trait Maker
{
    protected function makeAddress(array $attributes = []): array
    {
        return array_merge([
            'salutation' => SalutationEnum::Mr->value,
            'company' => 'Neuron GmbH',
            'first_name' => 'Max',
            'last_name' => 'Mustermann',
            'street' => 'Musterstraße',
            'number' => '10a',
            'additional' => 'Hinterhaus 2.OG',
            'postal_code' => '10249',
            'city' => 'Berlin',
            'country_code' => 'DE',
        ], $attributes);
    }
}
