<?php

namespace Tests;

use App\Condition\Presets\OrderValueIsGreaterOrEqualToAmount;
use App\Consequence\Presets\PercentageDiscountOnAllProducts;
use App\Enums\Payment\PaymentMethodEnum;
use App\Enums\Product\ProductTypeEnum;
use App\Exceptions\Order\PolicyException;
use App\Facades\Coupon;
use App\Facades\Stock;
use App\Models\Integration\Inventory\NeuronInventory;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;

trait Api
{
    // <editor-fold desc="Api">

    public function apiSalesChannelCreate(string $token, array $attributes = []): TestResponse
    {
        return $this->withToken($token)->postJson(
            '/api/sales-channel/create',
            array_merge([
                'name' => 'Neuron Shop',
                'currency_code' => 'EUR',
                'locale' => 'de_DE',
                'domains' => ['neuron.ddev.site'], // @todo
                'use_stock' => true,
                'remove_items_on_price_increase' => true,
                'checkout_summary_url' => 'https://www.merchant.de/checkout/summary',
                'order_summary_url' => 'https://www.merchant.de/order/summary'
            ], $attributes)
        );
    }

    public function apiShippingCreate(string $token, array $attributes = []): TestResponse
    {
        return $this->withToken($token)->postJson(
            '/api/shipping/create',
            array_merge([
                'name' => 'Shipping Provider',
                'enabled' => true,
                'country_code' => 'DE',
                'net_price' => 300,
                'gross_price' => 350,
                'currency_code' => 'EUR',
                'position' => 0,
            ], $attributes)
        );
    }

    public function apiNeuronPaymentCreate(string $token, array $attributes = []): TestResponse
    {
        return $this->withToken($token)->postJson(
            '/api/integration/payment-provider/neuron-payment/create',
            array_merge([
                'name' => 'Neuron Payment',
                'enabled' => true,
            ], $attributes)
        );
    }

    public function apiPaymentCreate(string $token, string $integrationId, string $integrationType, array $attributes = []): TestResponse
    {
        // @todo $integrationType over api?

        return $this->withToken($token)->postJson(
            '/api/payment/create',
            array_merge([
                'enabled' => true,
                'name' => Str::ucfirst(PaymentMethodEnum::Creditcard->value),
                'method' => PaymentMethodEnum::Creditcard->value,
                'position' => 1,
                'description' => 'Creditcard',
            ], $attributes, [
                'integration_id' => $integrationId,
                'integration_type' => $integrationType,
            ])
        );
    }

    public function apiNeuronInventoryCreate(string $token, array $attributes = []): TestResponse
    {
        return $this->withToken($token)->postJson(
            '/api/integration/inventory/neuron-inventory/create',
            array_merge([
                'name' => 'Neuron Inventory',
                'enabled' => true,
                'receive_inventory' => true,
                'distribute_order' => true,
            ], $attributes)
        );
    }

    public function apiProductCreate(string $token, array $attributes = [], int $initialStockQuantity = 100): TestResponse
    {
        $attributes = array_merge([
            'inventory_id' => '100001',
            'enabled' => true,
            'type' => ProductTypeEnum::Product->value,
            'sku' => 'product-abc',
            'name' => 'Product ABC',
            'net_price' => 1000,
            'gross_price' => 1100,
            'configuration' => null
        ], $attributes);

        $response = $this->withToken($token)->postJson('/api/product/create', $attributes);

        if ($attributes['type'] === ProductTypeEnum::Product->value && $initialStockQuantity > 0) {
            Stock::add($response->json()['data']['id'], $initialStockQuantity);
        }

        return $response;
    }

    public function apiProductUpdate(string $token, string $productId, array $attributes): TestResponse
    {
        return $this->withToken($token)->postJson(
            '/api/product/update',
            array_merge($attributes, [
                'id' => $productId
            ])
        );
    }

    /**
     * @throws Exception
     */
    public function apiConditionCreate(string $token, array $attributes = []): TestResponse
    {
        return $this->withToken($token)->postJson(
            '/api/condition/create',
            array_merge([
                'name' => OrderValueIsGreaterOrEqualToAmount::name(),
                'collection' => OrderValueIsGreaterOrEqualToAmount::make(1000)->toArray(),
            ], $attributes)
        );
    }

    public function deleteApiConditionDelete(string $token, string $conditionId): TestResponse
    {
        return $this->deleteJson('/api/condition/delete', ['id' => $conditionId]);
    }

    /**
     * @throws Exception
     */
    public function apiRuleCreate(string $token, string $conditionId, array $attributes = []): TestResponse
    {
        return $this->withToken($token)->postJson(
            '/api/rule/create',
            array_merge([
                'condition_id' => $conditionId,
                'name' => PercentageDiscountOnAllProducts::name(),
                'consequences' => PercentageDiscountOnAllProducts::make(10)->toArray(),
                'position' => 0,
                'enabled' => true,
            ], $attributes)
        );
    }

    public function apiCouponCreate(string $token, string $ruleId, array $attributes = []): TestResponse
    {
        $name = DB::table('rules')->where('id', $ruleId)->value('name');

        return $this->withToken($token)->postJson(
            '/api/coupon/create',
            array_merge([
                'rule_id' => $ruleId,
                'name' => $name,
                'code' => Coupon::generateCode(),
                'enabled' => true,
                'combinable' => false,
            ], $attributes)
        );
    }

    public function apiActionRuleCreate(string $token, string $conditionId, string $action, array $attributes = []): TestResponse
    {
        $conditionName = DB::table('conditions')->where('id', $conditionId)->value('name');

        return $this->withToken($token)->postJson(
            '/api/action-rule/create',
            array_merge([
                'condition_id' => $conditionId,
                'name' => $action . ' if ' . $conditionName,
                'action' => $action,
                'enabled' => true,
            ], $attributes)
        );
    }

    public function apiActionRuleDelete(string $token, string $actionRuleId): TestResponse
    {
        return $this->withToken($token)->deleteJson('/api/action-rule/delete', ['id' => $actionRuleId]);
    }

    public function apiCartRuleCreate(string $token, string $ruleId, array $attributes = []): TestResponse
    {
        $ruleName = DB::table('rules')->where('id', $ruleId)->value('name');

        return $this->withToken($token)->postJson(
            '/api/cart-rule/create',
            array_merge([
                'rule_id' => $ruleId,
                'name' => $ruleName,
                'enabled' => true,
            ], $attributes)
        );
    }

    public function apiCustomerCreate(string $token, string $email, string $fullName, array $attributes = []): TestResponse
    {
        return $this->withToken($token)->postJson(
            '/api/customer/create',
            array_merge([
                'email' => $email,
                'full_name' => $fullName,
            ], $attributes)
        );
    }


    // </editor-fold>

    // <editor-fold desc="Cart">

    public function apiCartGet(string $token, string $cartId): TestResponse
    {
        return $this->withToken($token)->get('cart/' . $cartId);
    }

    public function apiCartCreate(string $token, string $shippingId, string $paymentId): TestResponse
    {
        return $this->withToken($token)->postJson(
            '/cart/create',
            [
                'shipping_id' => $shippingId,
                'payment_id' => $paymentId,
            ]
        );
    }

    public function apiCartItemAdd(string $token,
        string $cartId,
        string $productId,
        int $quantity,
        array $configuration = null
    ): TestResponse {
        return $this->withToken($token)->postJson(
            '/cart/item/add',
            [
                'cart_id' => $cartId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'configuration' => $configuration
            ]
        );
    }

    public function apiCartItemUpdate(string $token, string $cartId, string $cartItemId, int $quantity): TestResponse
    {
        return $this->withToken($token)->postJson(
            '/cart/item/update',
            [
                'cart_id' => $cartId,
                'cart_item_id' => $cartItemId,
                'quantity' => $quantity
            ]
        );
    }

    public function apiCartUpdateCustomer(string $token, string $cartId, array $attributes): TestResponse
    {
        return $this->withToken($token)->postJson(
            '/cart/update/customer',
            array_merge($attributes, [
                'cart_id' => $cartId,
            ])
        );
    }

    public function apiCartUpdatePayment(string $token, string $cartId, string $paymentId): TestResponse
    {
        return $this->withToken($token)->postJson(
            '/cart/update/payment',
            [
                'cart_id' => $cartId,
                'payment_id' => $paymentId,
            ]
        );
    }

    public function apiCartUpdateShipping(string $token, string $cartId, string $shippingId): TestResponse
    {
        return $this->withToken($token)->postJson(
            '/cart/update/shipping',
            [
                'cart_id' => $cartId,
                'shipping_id' => $shippingId,
            ]
        );
    }

    public function apiCartCouponRedeem(string $token, string $cartId, string $code): TestResponse
    {
        return $this->withToken($token)->postJson(
            '/cart/coupon/redeem',
            [
                'cart_id' => $cartId,
                'code' => $code,
            ]
        );
    }

    public function apiCartCouponRemove(string $token, string $cartId, string $code): TestResponse
    {
        return $this->withToken($token)->postJson(
            '/cart/coupon/remove',
            [
                'cart_id' => $cartId,
                'code' => $code,
            ]
        );
    }

    public function apiCartPlace(string $token, string $cartId): TestResponse
    {
        return $this->withToken($token)->postJson(
            '/cart/place',
            [
                'cart_id' => $cartId
            ]
        );
    }

    // </editor-fold>

    // <editor-fold desc="Order">

    /**
     * @throws ValidationException
     * @throws PolicyException
     */
    public function apiCart(string $token, ): array
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];
        $salesChannelCartToken = $salesChannelCreateResponse->json()['data']['cart_token'];

        $neuronInventoryCreateResponse = $this->apiNeuronInventoryCreate($salesChannelToken);

        $neuronPaymentCreateResponse = $this->apiNeuronPaymentCreate($salesChannelToken);

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken, [
                'country_code' => 'DE',
            ]);

        $paymentCreateResponse = $this->apiPaymentCreate($salesChannelToken,
                $neuronPaymentCreateResponse->json()['data']['id'],
                NeuronPayment::class,
                [
                    'name' => Str::ucfirst(PaymentMethodEnum::Creditcard->value),
                    'method' => PaymentMethodEnum::Creditcard->value,
                ]
            );

        $productCreateResponse = $this->apiProductCreate($salesChannelToken, [
                'inventoryable_type' => NeuronInventory::class,
                'inventoryable_id' => $neuronInventoryCreateResponse->json()['data']['id'],
            ]);

        $orderCreateResponse = $this->apiCartCreate($salesChannelCartToken,
                $shippingCreateResponse->json()['data']['id'],
                $paymentCreateResponse->json()['data']['id'],
            );

        $updateCustomerResponse = $this->apiCartUpdateCustomer($salesChannelCartToken,
                $orderCreateResponse->json()['data']['id'],
                [
                    'email' => 'customer@neuron.de',
                    'shipping_address' => $this->makeAddress([
                        'country_code' => 'DE'
                    ]),
                ]
            );

        return [
            'salesChannelCreateResponse' => $salesChannelCreateResponse,
            'neuronInventoryCreateResponse' => $neuronInventoryCreateResponse,
            'neuronPaymentCreateResponse' => $neuronPaymentCreateResponse,
            'shippingCreateResponse' => $shippingCreateResponse,
            'paymentCreateResponse' => $paymentCreateResponse,
            'createResponse' => $updateCustomerResponse,
            'productCreateResponse' => $productCreateResponse,
        ];
    }

    // </editor-fold>
}
