<?php

namespace App\Actions\Engine\Address;

use App\Enums\Address\SalutationEnum;
use Illuminate\Validation\Rules\Enum;

class AddressCreateAction extends AddressAction
{
    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid|exists:sales_channels,id',
            'customer_id' => 'required|uuid|exists:customers,id',
            'company' => 'nullable|string',
            'salutation' => ['required', new Enum(SalutationEnum::class)],
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'street' => 'required|string',
            'number' => 'required|string',
            'additional' => 'nullable|string',
            'postal_code' => 'required|numeric',
            'city' => 'required|string',
            'country_code' => 'required|string|size:2', // ISO 3166-1 alpha-2
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
