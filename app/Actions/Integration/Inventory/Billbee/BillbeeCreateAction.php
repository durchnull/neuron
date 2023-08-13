<?php

namespace App\Actions\Integration\Inventory\Billbee;

class BillbeeCreateAction extends BillbeeAction
{

    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid|exists:sales_channels,id',
            'enabled' => 'required|boolean',
            'receive_inventory' => 'required|boolean',
            'distribute_order' => 'required|boolean',
            'name' => 'required|string|min:3',
            'user' => 'required|string',
            'api_password' => 'required|string',
            'api_key' => 'required|string',
            'shop_id' => 'required|string',
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
