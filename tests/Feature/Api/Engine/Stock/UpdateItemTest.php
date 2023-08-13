<?php

namespace Tests\Feature\Api\Engine\Stock;

use App\Enums\Order\PolicyReasonEnum;
use App\Enums\Payment\PaymentMethodEnum;
use App\Facades\Stock;
use App\Models\Integration\Inventory\NeuronInventory;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_quantity_can_not_exceed_stock(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken, [
                'country_code' => 'DE',
            ]);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelToken);

        $paymentCreateResponse = $this->apiPaymentCreate($salesChannelToken,
                $neuronPaymentCreateResponse->json()['data']['id'],
                NeuronPayment::class,
                [
                    'name' => Str::ucfirst(PaymentMethodEnum::Creditcard->value),
                    'method' => PaymentMethodEnum::Creditcard->value,
                ]
            );

        $cartCreateResponse = $this->apiCartCreate($salesChannelCartToken,
                $shippingCreateResponse->json()['data']['id'],
                $paymentCreateResponse->json()['data']['id'],
            );

        $neuronInventoryCreateResponse = $this->apiNeuronInventoryCreate($salesChannelToken);

        $productCreateResponse = $this->apiProductCreate($salesChannelToken, [
                'inventoryable_type' => NeuronInventory::class,
                'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
            ], 10);

        $this->assertEquals(10, Stock::get($productCreateResponse->json()['data']['id']));

        $cartAddResponse = $this->apiCartItemAdd($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                $productCreateResponse->json()['data']['id'],
                5
            );

        $cartAddResponse->assertStatus(200);

        $cartUpdateResponse = $this->apiCartItemUpdate($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                $cartAddResponse->json()['data']['items'][0]['id'],
                10
            );

        $cartUpdateResponse->assertStatus(200);

        $cartUpdateResponse2 = $this->apiCartItemUpdate($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                $cartUpdateResponse->json()['data']['items'][0]['id'],
                11
            );

        $cartUpdateResponse2->assertStatus(429);
        $this->assertEquals(PolicyReasonEnum::OutOfStock->value, $cartUpdateResponse2->json()['code']);
    }
}
