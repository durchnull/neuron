<?php

namespace Tests\Feature\Api\Engine\Cart;

use App\Condition\Presets\Unconditional;
use App\Consequence\Presets\FreeShipping;
use App\Consequence\Presets\PercentageDiscountOnAllProducts;
use App\Enums\Order\OrderStatusEnum;
use App\Enums\Payment\PaymentMethodEnum;
use App\Enums\Transaction\TransactionStatusEnum;
use App\Exceptions\Order\PolicyException;
use App\Models\Engine\Transaction;
use App\Models\Integration\Inventory\NeuronInventory;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws ValidationException
     * @throws PolicyException
     */
    public function test_order_with_one_free_synchronous_transaction(): void
    {
        // A placed cart with a 0 amount and a free payment method
        $cartPlaceResponse = $this->getCartPlaceResponse(
            PaymentMethodEnum::Creditcard,
            true,
            true
        );

        $this->assertEquals(OrderStatusEnum::Confirmed->value, $cartPlaceResponse['response']->json()['data']['status']);
        $this->assertCount(1, $cartPlaceResponse['response']->json()['data']['transactions']);
        $this->assertEquals(TransactionStatusEnum::Paid->value, $cartPlaceResponse['response']->json()['data']['transactions'][0]['status']);
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     */
    public function test_order_with_one_pending_synchronous_transaction(): void
    {
        $cartPlaceResponse = $this->getCartPlaceResponse(PaymentMethodEnum::Prepayment);

        $this->assertEquals(OrderStatusEnum::Accepted->value, $cartPlaceResponse['response']->json()['data']['status']);
        $this->assertCount(1, $cartPlaceResponse['response']->json()['data']['transactions']);
        $this->assertEquals(TransactionStatusEnum::Pending->value, $cartPlaceResponse['response']->json()['data']['transactions'][0]['status']);
    }

    /**
     * @throws Exception
     */
    public function test_order_with_one_paid_asynchronous_transaction(): void
    {
        $cartPlaceResponse = $this->getCartPlaceResponse(PaymentMethodEnum::Creditcard);

        $this->assertEquals(5, $cartPlaceResponse['response']->json()['data']['version']);
        $this->assertEquals(OrderStatusEnum::Accepted->value, $cartPlaceResponse['response']->json()['data']['status']);
        $this->assertCount(1, $cartPlaceResponse['response']->json()['data']['transactions']);
        $this->assertEquals(TransactionStatusEnum::Pending->value, $cartPlaceResponse['response']->json()['data']['transactions'][0]['status']);

        // @todo [test] revisit, should processPaymentProviderTransaction be refactored? transaction id sounds better to be left out of CartTransactionResource
        $transactionId = Transaction::where('order_id', $cartPlaceResponse['response']->json()['data']['id'])->get()->first()->id;

        $this->processPaymentProviderTransaction(
            $transactionId,
            TransactionStatusEnum::Paid
        );

        $cartShowResponse = $this->apiCartGet(
            $cartPlaceResponse['sales_channel_cart_token'],
            $cartPlaceResponse['response']->json()['data']['id']
        );

        $this->assertEquals(6, $cartShowResponse->json()['data']['version']);
        $this->assertEquals(OrderStatusEnum::Confirmed->value, $cartShowResponse->json()['data']['status']);
        $this->assertCount(1, $cartShowResponse->json()['data']['transactions']);
        $this->assertEquals(TransactionStatusEnum::Paid->value, $cartShowResponse->json()['data']['transactions'][0]['status']);
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    protected function getCartPlaceResponse(
        PaymentMethodEnum $paymentMethod,
        bool $freeShipping = false,
        bool $freeItems = false
    ): array {
        if ($paymentMethod === PaymentMethodEnum::Free) {
            throw new Exception('Free payment method will be created automatically. Use a different one.');
        }

        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken, [
            'country_code' => 'DE',
        ]);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelToken);

        $paymentCreate1Response = $this->apiPaymentCreate(
            $salesChannelToken,
            $neuronPaymentCreateResponse->json()['data']['id'],
            NeuronPayment::class,
            [
                'enabled' => true,
                'name' => Str::ucfirst(PaymentMethodEnum::Free->value),
                'method' => PaymentMethodEnum::Free->value,
                'position' => 1,
                'description' => 'Free payment',
            ]
        );

        $paymentCreate2Response = $this->apiPaymentCreate(
            $salesChannelToken,
            $neuronPaymentCreateResponse->json()['data']['id'],
            NeuronPayment::class,
            [
                'name' => Str::ucfirst($paymentMethod->value),
                'method' => $paymentMethod->value,
                'position' => 2,
                'description' => Str::ucfirst($paymentMethod->value),
            ]
        );

        $cartCreateResponse = $this->apiCartCreate(
            $salesChannelCartToken,
            $shippingCreateResponse->json()['data']['id'],
            $paymentCreate2Response->json()['data']['id']
        );

        $neuronInventoryCreateResponse = $this->apiNeuronInventoryCreate($salesChannelToken);

        $productCreateResponse = $this->apiProductCreate($salesChannelToken, [
            'inventoryable_type' => NeuronInventory::class,
            'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
        ]);

        $cartAddResponse = $this->apiCartItemAdd(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            $productCreateResponse->json()['data']['id'],
            1
        );

        $updateCustomerResponse = $this->apiCartUpdateCustomer(
            $salesChannelCartToken,
            $cartCreateResponse->json()['data']['id'],
            [
                'email' => 'customer@neuron.de',
                'shipping_address' => $this->makeAddress([
                    'country_code' => 'DE'
                ]),
            ]
        );

        if ($freeShipping || $freeItems) {
            $conditionCreateResponse = $this->apiConditionCreate($salesChannelToken, [
                'name' => Unconditional::name(),
                'collection' => Unconditional::make()->toArray(),
            ]);

            $ruleCreateResponse = $this->apiRuleCreate(
                $salesChannelToken,
                $conditionCreateResponse->json()['data']['id'],
                [
                    'name' => 'Some things are free',
                    'consequences' => array_filter(
                        array_merge(
                            $freeItems ? PercentageDiscountOnAllProducts::make(100)->toArray() : null,
                            $freeShipping ? FreeShipping::make()->toArray() : null
                        )
                    ),
                    'enabled' => true
                ]
            );

            $couponCreateResponse = $this->apiCouponCreate(
                $salesChannelToken,
                $ruleCreateResponse->json()['data']['id'],
                [
                    'name' => $ruleCreateResponse->json()['data']['name'],
                    'code' => 'FREE',
                    'enabled' => true
                ]
            );

            $couponRedemptionResponse = $this->apiCartCouponRedeem(
                $salesChannelCartToken,
                $cartCreateResponse->json()['data']['id'],
                $couponCreateResponse->json()['data']['code']
            );

            $couponRedemptionResponse->assertStatus(200);
        }

        return [
            'response' => $this->apiCartPlace($salesChannelCartToken, $cartCreateResponse->json()['data']['id']),
            'sales_channel_token' => $salesChannelToken,
            'sales_channel_cart_token' => $salesChannelCartToken,
        ];
    }
}
