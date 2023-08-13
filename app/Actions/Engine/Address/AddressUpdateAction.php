<?php

namespace App\Actions\Engine\Address;

use App\Enums\Address\SalutationEnum;
use Illuminate\Validation\Rules\Enum;

class AddressUpdateAction extends AddressAction
{
    public static function rules(): array
    {
        return [
            'customer_id' => 'nullable|uuid|exists:customers,id',
            'company' => 'nullable|string',
            'salutation' => ['nullable', new Enum(SalutationEnum::class)],
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'street' => 'nullable|string',
            'number' => 'nullable|string',
            'additional' => 'nullable|string',
            'postal_code' => 'nullable|numeric',
            'city' => 'nullable|string',
            'country_code' => 'nullable|string|size:2', // ISO 3166-1 alpha-2
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
