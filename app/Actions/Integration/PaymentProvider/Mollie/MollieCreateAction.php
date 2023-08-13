<?php

namespace App\Actions\Integration\PaymentProvider\Mollie;

class MollieCreateAction extends MollieAction
{

    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid|exists:sales_channels,id',
            'enabled' => 'required|boolean',
            'name' => 'required|string|min:3',
            'api_key' => 'required|string|starts_with:test_,live_', // ! preg_match('/^(live|test)_\w{30,}$/', $apiKey)
            'profile_id' => 'required|string|starts_with:pfl_'
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
