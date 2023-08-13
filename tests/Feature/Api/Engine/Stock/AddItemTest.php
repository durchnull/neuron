<?php

namespace Tests\Feature\Api\Engine\Stock;

use App\Enums\Order\PolicyReasonEnum;
use App\Enums\Payment\PaymentMethodEnum;
use App\Exceptions\Order\PolicyException;
use App\Facades\Stock;
use App\Models\Integration\Inventory\NeuronInventory;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AddItemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws ValidationException
     * @throws PolicyException
     */
    public function test_stock_is_unchanged_after_adding_item(): void
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
                1
            );

        $cartAddResponse->assertStatus(200);
        $this->assertEquals(10, Stock::get($productCreateResponse->json()['data']['id']));
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     */
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

        $cartAddResponse = $this->apiCartItemAdd($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                $productCreateResponse->json()['data']['id'],
                6
            );

        $cartAddResponse->assertStatus(429);
        $this->assertEquals(PolicyReasonEnum::OutOfStock->value, $cartAddResponse->json()['code']);
    }
}
