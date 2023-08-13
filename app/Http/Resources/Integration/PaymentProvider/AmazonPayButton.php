<?php

namespace App\Http\Resources\Integration\PaymentProvider;

use App\Contracts\Integration\PaymentProvider\AmazonPayServiceContract;
use App\Facades\Integrations;
use App\Facades\SalesChannel;
use App\Models\Integration\PaymentProvider\AmazonPay;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AmazonPayButton extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     * @throws Exception
     */
    public function toArray(Request $request): array
    {
        $paymentProvider = Integrations::getPaymentProvider($this->resource->payment->integration);

        if (!$paymentProvider instanceof AmazonPayServiceContract) {
            throw new \Exception('Cant use instantiate correct payment provider');
        }

        $payload = $paymentProvider->makeCheckoutSessionPayload($this->resource);
        // @todo cache payload
        $signature = $paymentProvider->generateButtonSignature($payload);

        return [
            'estimatedOrderAmount' => [
                'amount' => number_format($this->amount / 100, 2, '.'),
                'currencyCode' => SalesChannel::get()->currency_code
            ],
            'createCheckoutSessionConfig' => [
                'payloadJSON' => json_encode($payload, JSON_UNESCAPED_SLASHES),
                'signature' => $signature,
                'algorithm' => $paymentProvider::getAlgorithm()
            ],
            'deliverySpecifications' => $payload['deliverySpecifications']
        ];
    }
}
