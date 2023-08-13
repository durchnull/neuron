<?php

namespace App\Actions\Integration\PaymentProvider\PostFinance;

class PostFinanceCreateAction extends PostFinanceAction
{

    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid|exists:sales_channels,id',
            'enabled' => 'required|boolean',
            'name' => 'required|string',
            'space_id' => 'required|string',
            'user_id' => 'required|string',
            'secret' => 'required|string',
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
