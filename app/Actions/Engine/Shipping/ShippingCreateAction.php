<?php

namespace App\Actions\Engine\Shipping;

class ShippingCreateAction extends ShippingAction
{

    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid',
            'enabled' => 'required|boolean',
            'name' => 'required|string',
            'country_code' => 'required|string|size:2', // ISO 3166-1 alpha-2
            'net_price' => 'required|integer|min:0',
            'gross_price' => 'required|integer|min:0',
            'currency_code' => 'required|in:EUR',
            'position' => 'required|integer|min:0',
            'default' => 'nullable|boolean',
        ];
    }

    protected function apply(): void
    {
        // @todo set default true if first record

        $this->target->fill($this->validated)->save();
    }
}
