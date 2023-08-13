<?php

namespace Tests\Feature\Api\Engine\Payment;

use App\Enums\Payment\PaymentMethodEnum;
use App\Exceptions\Order\PolicyException;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws ValidationException
     * @throws PolicyException
     */
    public function test_payment_create(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelCreateResponse->json()['data']['token']);

        $paymentCreateResponse = $this->apiPaymentCreate($salesChannelCreateResponse->json()['data']['token'],
                $neuronPaymentCreateResponse->json()['data']['id'],
                NeuronPayment::class,
                [
                    'name' => Str::ucfirst(PaymentMethodEnum::Creditcard->value),
                    'method' => PaymentMethodEnum::Creditcard->value,
                ]
            );

        $paymentCreateResponse->assertStatus(201);
    }
}
