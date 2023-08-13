<?php

namespace App\Http\Resources\Integration\PaymentProvider;

use App\Contracts\Integration\PaymentProvider\AmazonPayServiceContract;
use App\Facades\Integrations;
use App\Facades\SalesChannel;
use App\Models\Integration\PaymentProvider\AmazonPay;
use App\Services\Integration\PaymentProvider\Resources\AmazonPayCheckoutSessionResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AmazonPayCheckoutSession extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     * @throws Exception
     */
    public function toArray(Request $request): array
    {
        $amazonCheckoutSessionId = $request->post('amazon_checkout_session_id');

        if (!$amazonCheckoutSessionId) {
            throw new Exception('No amazon_checkout_session_id');
        }

        $paymentProvider = Integrations::getPaymentProvider($this->resource->payment->integration);

        if (!$paymentProvider instanceof AmazonPayServiceContract) {
            throw new \Exception('Cant use instantiate correct payment provider');
        }

        return $paymentProvider
            ->getResource($amazonCheckoutSessionId)
            ->getCustomerProfile()
            ->toArray();
    }
}
