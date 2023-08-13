<?php

namespace App\Actions\Engine\Shipping;

class ShippingUpdateAction extends ShippingAction
{

    public static function rules(): array
    {
        return [
            'enabled' => 'nullable|boolean',
            'name' => 'nullable|string',
            'country_code' => 'nullable|string|size:2',
            'net_price' => 'nullable|integer|min:0',
            'gross_price' => 'nullable|integer|min:0',
            'currency_code' => 'nullable|in:EUR',
            'position' => 'nullable|integer|min:0',
            'default' => 'nullable|boolean',
        ];
    }

    protected function apply(): void
    {
        // @todo set default false to all if default is true

        $this->target->fill($this->validated)->save();
    }
}
