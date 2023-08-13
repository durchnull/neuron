<?php

namespace App\Actions\Engine\SalesChannel;

use App\Generators\TokenGenerator;
use Exception;

class SalesChannelCreateAction extends SalesChannelAction
{
    public static function rules(): array
    {
        return [
            'merchant_id' => 'required|uuid|exists:merchants,id',
            'name' => 'required|string|min:3',
            'currency_code' => 'required|in:EUR,CHF',
            'domains' => 'required|array',
            'locale' => 'required|string|min:5', // @todo [validation] in:de_DE ISO-3166
            'use_stock' => 'required|boolean',
            'remove_items_on_price_increase' => 'required|boolean',
            'checkout_summary_url' => 'required|url',
            'order_summary_url' => 'required|url',
        ];
    }

    protected function gate(array $attributes): void
    {
    }

    /**
     * @throws Exception
     */
    protected function apply(): void
    {
        $this->target->fill(array_merge($this->validated, [
            'token' => TokenGenerator::make()->generate(),
            'cart_token' => TokenGenerator::make()->generate(),
        ]))->save();
    }
}
