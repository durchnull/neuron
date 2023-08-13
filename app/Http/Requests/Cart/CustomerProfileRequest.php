<?php

namespace App\Http\Requests\Cart;

use App\Enums\Address\SalutationEnum;
use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rules\Enum;

class CustomerProfileRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'order_id' => 'required|uuid',
            // customer
            'email' => 'required|email',
            'phone' => 'nullable|string',
            // customer additional
            'note' => 'nullable|string',
            // shipping address
            'shipping_address' => 'required|array',
            'shipping_address.salutation' => ['required', new Enum(SalutationEnum::class)],
            'shipping_address.company' => 'nullable|string',
            'shipping_address.first_name' => 'required|string',
            'shipping_address.last_name' => 'required|string',
            'shipping_address.street' => 'required|string',
            'shipping_address.number' => 'required|string',
            'shipping_address.additional' => 'nullable|string',
            'shipping_address.postal_code' => 'required|numeric',
            'shipping_address.city' => 'required|string',
            'shipping_address.country_code' => 'required|string|size:2', // ISO 3166-1 alpha-2
            // billing address
            'billing_address' => 'nullable|array',
            'billing_address.salutation' => ['nullable', new Enum(SalutationEnum::class)],
            'billing_address.company' => 'nullable|string',
            'billing_address.first_name' => 'nullable|string|required_with:billing_address',
            'billing_address.last_name' => 'nullable|string|required_with:billing_address',
            'billing_address.street' => 'nullable|string|required_with:billing_address',
            'billing_address.number' => 'nullable|string|required_with:billing_address',
            'billing_address.additional' => 'nullable|string',
            'billing_address.postal_code' => 'nullable|numeric|required_with:billing_address',
            'billing_address.city' => 'nullable|string|required_with:billing_address',
            'billing_address.country_code' => 'nullable|string|size:2|required_with:billing_address',
        ];
    }
}
