<?php

namespace App\Actions\Integration\Inventory\Weclapp;

class WeclappCreateAction extends WeclappAction
{

    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid|exists:sales_channels,id',
            'enabled' => 'required|boolean',
            'receive_inventory' => 'required|boolean',
            'distribute_order' => 'required|boolean',
            'name' => 'required|string|min:3',
            'url' => 'required|string',
            'api_token' => 'required|string',
            'article_category_id' => 'nullable|string',
            'distribution_channel' => 'nullable|string',
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
