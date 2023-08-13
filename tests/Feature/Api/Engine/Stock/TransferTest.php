<?php

namespace Tests\Feature\Api\Engine\Stock;

use App\Enums\Payment\PaymentMethodEnum;
use App\Facades\Stock;
use App\Models\Integration\Inventory\NeuronInventory;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TransferTest extends TestCase
{
    use RefreshDatabase;

    public static function provideMovements(): array
    {
        return [
            [
                'initialStock' => 10,
                'addedQuantity' => 5,
                'stockAfterPlace' => 5,
                'queueAfterPlace' => 5,
            ],
            [
                'initialStock' => 5,
                'addedQuantity' => 5,
                'stockAfterPlace' => 0,
                'queueAfterPlace' => 5,
            ]
        ];
    }

    /**
     * @dataProvider provideMovements
     */
    public function test_stock_moves_to_queue_on_cart_place(
        int $initialStock,
        int $addedQuantity,
        int $stockAfterPlace,
        int $queueAfterPlace,
    ): void {
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

        $this->assertEquals(1, $cartCreateResponse->json()['data']['version']);

        $neuronInventoryCreateResponse = $this->apiNeuronInventoryCreate($salesChannelToken);

        $productCreateResponse = $this->apiProductCreate($salesChannelToken, [
                'inventoryable_type' => NeuronInventory::class,
                'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
            ], $initialStock);

        $cartAddResponse = $this->apiCartItemAdd($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                $productCreateResponse->json()['data']['id'],
                $addedQuantity
            );

        $this->assertEquals(2, $cartAddResponse->json()['data']['version']);

        $updateCustomerResponse = $this->apiCartUpdateCustomer($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                [
                    'email' => 'customer@neuron.de',
                    'shipping_address' => $this->makeAddress([
                        'country_code' => 'DE'
                    ]),
                ]
            );

        $this->assertEquals(3, $updateCustomerResponse->json()['data']['version']);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelCreateResponse->json()['data']['token']);

        $paymentCreateResponse = $this->apiPaymentCreate($salesChannelToken,
                $neuronPaymentCreateResponse->json()['data']['id'],
                NeuronPayment::class,
                [
                    'name' => Str::ucfirst(PaymentMethodEnum::Prepayment->value),
                    'method' => PaymentMethodEnum::Prepayment->value,
                    'position' => 1,
                    'description' => 'Prepayment',
                ]
            );

        $cartUpdateResponse = $this->apiCartUpdatePayment($salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                $paymentCreateResponse->json()['data']['id']
            );

        $this->assertEquals(4, $cartUpdateResponse->json()['data']['version']);

        $this->assertEquals($initialStock, Stock::get($productCreateResponse->json()['data']['id']));
        $this->assertEquals(0, Stock::get($productCreateResponse->json()['data']['id'], true));

        $cartPlaceResponse = $this->apiCartPlace($salesChannelCartToken, $cartCreateResponse->json()['data']['id']);

        $this->assertEquals(6, $cartPlaceResponse->json()['data']['version']);
        $this->assertEquals($stockAfterPlace, Stock::get($productCreateResponse->json()['data']['id']));
        $this->assertEquals($queueAfterPlace, Stock::get($productCreateResponse->json()['data']['id'], true));
    }
}
