<?php

namespace App\Http\Resources\Engine;

use App\Enums\Payment\PaymentMethodEnum;
use App\Models\Integration\PaymentProvider\AmazonPay;
use App\Models\Integration\PaymentProvider\Mollie;
use App\Models\Integration\PaymentProvider\Paypal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $init = null;

        if ($this->integration_type === AmazonPay::class) {
            $init = 'amazon-pay-button';
        } elseif ($this->integration_type === Mollie::class && $this->method === PaymentMethodEnum::Creditcard) {
            $init = 'mollie-card';
        } elseif ($this->integration_type === Paypal::class) {
            $init = 'paypal';
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'method' => $this->method,
            'position' => $this->position,
            'description' => $this->description,
            'default' => $this->default,
            'init' => $init
        ];
    }
}
