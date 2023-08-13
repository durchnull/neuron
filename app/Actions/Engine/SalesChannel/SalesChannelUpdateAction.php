<?php

namespace App\Actions\Engine\SalesChannel;

class SalesChannelUpdateAction extends SalesChannelAction
{
    public static function rules(): array
    {
        return [
            'name' => 'nullable|string|min:3',
            'currency_code' => 'nullable|in:EUR,CHF',
            'locale' => 'nullable|string|min:5', // @todo [validation] in:de_DE ISO-3166
            'use_stock' => 'nullable|boolean',
            'remove_items_on_price_increase' => 'nullable|boolean',
            'checkout_summary_url' => 'nullable|url',
            'order_summary_url' => 'nullable|url',
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
