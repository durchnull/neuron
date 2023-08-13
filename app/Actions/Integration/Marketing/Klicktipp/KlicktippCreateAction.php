<?php

namespace App\Actions\Integration\Marketing\Klicktipp;

class KlicktippCreateAction extends KlicktippAction
{

    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid|exists:sales_channels,id',
            'enabled' => 'required|boolean',
            'distribute_order' => 'required|boolean',
            'name' => 'required|string|min:3',
            'tag_prefix' => 'required|string',
            'user_name' => 'required|string',
            'developer_key' => 'required|string',
            'customer_key' => 'required|string',
            'service' => 'required|string',
            'tags' => 'nullable|array',
            'tags_coupons' => 'nullable|array',
            'tags_periods' => 'nullable|array',
            'tags_new_customer' => 'nullable|array',
            'tags_products' => 'nullable|array',
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
