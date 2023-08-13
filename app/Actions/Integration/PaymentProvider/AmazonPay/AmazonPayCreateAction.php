<?php

namespace App\Actions\Integration\PaymentProvider\AmazonPay;

class AmazonPayCreateAction extends AmazonPayAction
{

    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid|exists:sales_channels,id',
            'enabled' => 'required|boolean',
            'name' => 'required|string',
            // Amazon Pay merchant account identifier
            'merchant_account_id' => 'required|string',
            // RSA Public Key ID (this is not the Merchant or Seller ID)
            'public_key_id' => 'required|string',
            // Path to RSA Private Key (or a string representation)
            'private_key' => 'required|string',
            'region' => 'required|string|in:us,eu,jp',
            'store_id' => 'required|string|starts_with:amzn1.application-oa2-client.',
            'sandbox' => 'required|bool',
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
