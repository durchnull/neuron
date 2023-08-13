<?php

namespace App\Http\Resources\Engine\Cart;

use App\Enums\Payment\PaymentMethodEnum;
use App\Facades\SalesChannel;
use App\Models\Integration\PaymentProvider\AmazonPay;
use App\Models\Integration\PaymentProvider\Mollie;
use App\Models\Integration\PaymentProvider\Paypal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class CartPaymentOptionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'provider' => Str::slug(class_basename($this->integration_type)),
            'method' => Str::slug($this->method->value),
            'name' => $this->name,
            'default' => $this->default,
            'position' => $this->position,
            'init' => $this->makeInit()
        ];
    }

    protected function makeInit(): ?array
    {
        switch (get_class($this->integration)) {
            case AmazonPay::class:
                return $this->makeAmazonPayButton($this->integration);
            case Mollie::class:
                if ($this->method === PaymentMethodEnum::Creditcard) {
                    return $this->makeMollieComponents($this->integration);
                }
                return null;
            case Paypal::class:
                return $this->makePaypalButtons($this->integration);
            default:
                return null;
        }
    }

    protected function makeAmazonPayButton(AmazonPay $integration): array
    {
        return [
            'region' => $integration->region,
            'merchantId' => $integration->merchant_account_id,
            'publicKeyId' => $integration->public_key_id,
            'ledgerCurrency' => SalesChannel::get()->currency_code,
            'checkoutLanguage' => SalesChannel::get()->locale, // @todo locale
            'productType' => 'PayAndShip',
            'placement' => 'Cart',
            'buttonColor' => 'Gold', // @todo customizable
        ];
    }

    protected function makeMollieComponents(Mollie $integration): array
    {
        $mollieComponents = [
            'profile_id' => $integration->profile_id,
            'locale' => SalesChannel::get()->locale,
        ];

        if (!App::environment('production')) {
            $mollieComponents['testmode'] = true;
        }

        return $mollieComponents;
    }

    protected function makePaypalButtons(Paypal $integration): array
    {
        return [
            'client_id' => $integration->client_id
        ];
    }
}
