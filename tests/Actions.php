<?php

namespace Tests;

use App\Actions\Engine\ActionRule\ActionRuleCreateAction;
use App\Actions\Engine\ActionRule\ActionRuleDeleteAction;
use App\Actions\Engine\CartRule\CartRuleCreateAction;
use App\Actions\Engine\Condition\ConditionCreateAction;
use App\Actions\Engine\Coupon\CouponCreateAction;
use App\Actions\Engine\Customer\CustomerCreateAction;
use App\Actions\Engine\Merchant\MerchantCreateAction;
use App\Actions\Engine\Order\OrderAddItemAction;
use App\Actions\Engine\Order\OrderCreateAction;
use App\Actions\Engine\Payment\PaymentCreateAction;
use App\Actions\Engine\Payment\PaymentUpdateAction;
use App\Actions\Engine\Product\ProductCreateAction;
use App\Actions\Engine\Product\ProductUpdateAction;
use App\Actions\Engine\Rule\RuleCreateAction;
use App\Actions\Engine\SalesChannel\SalesChannelCreateAction;
use App\Actions\Engine\Shipping\ShippingCreateAction;
use App\Actions\Engine\Transaction\TransactionCreateAction;
use App\Actions\Integration\Inventory\NeuronInventory\NeuronInventoryCreateAction;
use App\Actions\Integration\PaymentProvider\NeuronPayment\NeuronPaymentCreateAction;
use App\Condition\Presets\OrderValueIsGreaterOrEqualToAmount;
use App\Consequence\Presets\PercentageDiscountOnAllProducts;
use App\Enums\Payment\PaymentMethodEnum;
use App\Enums\Product\ProductTypeEnum;
use App\Enums\Transaction\TransactionStatusEnum;
use App\Enums\TriggerEnum;
use App\Exceptions\Order\PolicyException;
use App\Models\Engine\ActionRule;
use App\Models\Engine\CartRule;
use App\Models\Engine\Condition;
use App\Models\Engine\Coupon;
use App\Models\Engine\Customer;
use App\Models\Engine\Merchant;
use App\Models\Engine\Order;
use App\Models\Engine\Payment;
use App\Models\Engine\Product;
use App\Models\Engine\Rule;
use App\Models\Engine\SalesChannel;
use App\Models\Engine\Shipping;
use App\Models\Engine\Transaction;
use App\Models\Integration\Inventory\NeuronInventory;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait Actions
{
    // <editor-fold desc="Engine">

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function actionActionRuleCreate(string $salesChannelId, string $conditionId, array $attributes = []): ActionRule
    {
        $actionRuleCreateAction = new ActionRuleCreateAction(
            new ActionRule(), array_merge([
            'sales_channel_id' => $salesChannelId,
            'condition_id' => $conditionId,
            'name' => 'Condition name',
            'action' => class_basename(OrderAddItemAction::class),
            'enabled' => true,
        ], $attributes), TriggerEnum::App
        );

        $actionRuleCreateAction->trigger();

        return $actionRuleCreateAction->target();
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function actionActionRuleDelete(ActionRule $actionRule): void
    {
        $actionRuleCreateAction = new ActionRuleDeleteAction($actionRule, [], TriggerEnum::App);

        $actionRuleCreateAction->trigger();
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function actionOrderCreate(
        string $salesChannelId,
        string $shippingId,
        string $paymentId,
        array $attributes = []
    ): Order {
        $orderCreateAction = new OrderCreateAction(
            new Order(),
            array_merge([
                'sales_channel_id' => $salesChannelId,
                'shipping_id' => $shippingId,
                'payment_id' => $paymentId
            ], $attributes),
            TriggerEnum::App
        );

        $orderCreateAction->trigger();

        return $orderCreateAction->target();
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function actionCartRuleCreate(
        string $salesChannelId,
        string $ruleId,
        array $attributes = []
    ): CartRule {
        $cartRuleCreateAction = new CartRuleCreateAction(
            new CartRule(),
            array_merge([
                'sales_channel_id' => $salesChannelId,
                'rule_id' => $ruleId,
                'name' => 'Winter Sale',
                'enabled' => true,
            ], $attributes),
            TriggerEnum::App
        );

        $cartRuleCreateAction->trigger();

        return $cartRuleCreateAction->target();
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function actionConditionCreate(string $salesChannelId, array $attributes = []): Condition
    {
        $actionCreateAction = new ConditionCreateAction(
            new Condition(),
            array_merge([
                'sales_channel_id' => $salesChannelId,
                'name' => OrderValueIsGreaterOrEqualToAmount::name('10'),
                'collection' => OrderValueIsGreaterOrEqualToAmount::make(1000)->toArray(),
            ], $attributes),
            TriggerEnum::App
        );

        $actionCreateAction->trigger();

        return $actionCreateAction->target();
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function actionCouponCreate(string $salesChannelId, string $ruleId, array $attributes = []): Coupon
    {
        $couponCreateAction = new CouponCreateAction(
            new Coupon(),
            array_merge([
                'sales_channel_id' => $salesChannelId,
                'rule_id' => $ruleId,
                'name' => 'Rule name',
                'code' => 'IAMACODE',
                'enabled' => true,
                'combinable' => false,
            ], $attributes),
            TriggerEnum::App
        );

        $couponCreateAction->trigger();

        return $couponCreateAction->target();
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function actionCustomerCreate(string $salesChannelId, array $attributes = []): Customer
    {
        $customerCreateAction = new CustomerCreateAction(
            new Customer(),
            array_merge([
                'sales_channel_id' => $salesChannelId,
                'email' => 'customer@neuron.de',
                'full_name' => 'First name last name',
                'phone' => '+49123456789'
            ], $attributes),
            TriggerEnum::App
        );

        $customerCreateAction->trigger();

        return $customerCreateAction->target();
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function actionMerchantCreate(array $attributes = []): Merchant
    {
        $merchantCreateAction = new MerchantCreateAction(
            new Merchant(),
            array_merge([
                'name' => 'Merchant'
            ], $attributes),
            TriggerEnum::App
        );

        $merchantCreateAction->trigger();

        return $merchantCreateAction->target();
    }


    /**
     * @throws Exception
     */
    public function actionNeuronPaymentCreate(string $salesChannelId): NeuronPayment
    {
        $neuronPaymentCreateAction = new NeuronPaymentCreateAction(new NeuronPayment(), [
            'sales_channel_id' => $salesChannelId,
            'enabled' => true,
            'name' => 'Neuron Payment',
        ], TriggerEnum::App);

        $neuronPaymentCreateAction->trigger();

        return $neuronPaymentCreateAction->target();
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function actionPaymentCreate(
        string $salesChannelId,
        string $integrationId,
        string $integrationType,
        array $attributes = []
    ): Payment {
        $paymentCreateAction = new PaymentCreateAction(
            new Payment(),
            array_merge([
                'sales_channel_id' => $salesChannelId,
                'integration_id' => $integrationId,
                'integration_type' => $integrationType,
                'enabled' => true,
                'name' => Str::ucfirst(PaymentMethodEnum::Creditcard->value),
                'method' => PaymentMethodEnum::Creditcard->value,
                'position' => 1,
                'description' => 'Pay with Creditcard',
            ], $attributes),
            TriggerEnum::App
        );

        $paymentCreateAction->trigger();

        return $paymentCreateAction->target();
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function actionPaymentUpdate(
        Payment $payment,
        array $attributes = []
    ): Payment {
        if (empty($attributes)) {
            throw new Exception('Attributes can not be empty');
        }

        $paymentUpdateAction = new PaymentUpdateAction(
            $payment,
            $attributes,
            TriggerEnum::App
        );

        $paymentUpdateAction->trigger();

        return $paymentUpdateAction->target();
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function actionProductCreate(
        string $salesChannelId,
        string $inventoryableId,
        string $inventoryableType,
        array $attributes = []
    ): Product {
        $productCreateAction = new ProductCreateAction(
            new Product(),
            array_merge([
                'sales_channel_id' => $salesChannelId,
                'inventoryable_type' => $inventoryableType,
                'inventoryable_id' => $inventoryableId,
                'inventory_id' => Str::uuid()->toString(),
                'enabled' => true,
                'name' => 'Bonbon',
                'type' => ProductTypeEnum::Product->value,
                'sku' => 'bonbon',
                'net_price' => 395,
                'gross_price' => 400,
                'configuration' => null,
                'url' => 'https://www.shop.de/product/bonbon',
                'image_url' => 'https://www.shop.de/bonbon.jpg',
            ], $attributes),
            TriggerEnum::App
        );

        $productCreateAction->trigger();

        return $productCreateAction->target();
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    public function actionProductUpdate(Product $product, array $attributes): Product
    {
        if (empty($attributes)) {
            throw new Exception('Attributes must not be empty');
        }

        $productUpdateAction = new ProductUpdateAction($product, $attributes, TriggerEnum::App);

        $productUpdateAction->trigger();

        return $productUpdateAction->target();
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function actionRuleCreate(
        string $salesChannelId,
        string $conditionId,
        array $attributes = []
    ): Rule {
        $ruleCreateAction = new RuleCreateAction(
            new Rule(),
            array_merge([
                'sales_channel_id' => $salesChannelId,
                'condition_id' => $conditionId,
                'name' => 'If ' . OrderValueIsGreaterOrEqualToAmount::name() . ' then apply ' . PercentageDiscountOnAllProducts::name(),
                'consequences' => PercentageDiscountOnAllProducts::make(10)->toArray(),
                'position' => 0,
            ], $attributes),
            TriggerEnum::App
        );

        $ruleCreateAction->trigger();

        return $ruleCreateAction->target();
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function actionSalesChannelCreate(string $merchantId, array $attributes = []): SalesChannel
    {
        $salesChannelCreateAction = new SalesChannelCreateAction(
            new SalesChannel(),
            array_merge([
                'merchant_id' => $merchantId,
                'currency_code' => 'EUR',
                'name' => 'Shop',
                'domains' => ['neuron.ddev.site'],
                'locale' => 'de_DE',
                'use_stock' => true,
                'remove_items_on_price_increase' => true,
                'checkout_summary_url' => 'https://www.merchant.de/checkout/summary',
                'order_summary_url' => 'https://www.merchant.de/order/summary'
            ], $attributes),
            TriggerEnum::App
        );

        $salesChannelCreateAction->trigger();

        return $salesChannelCreateAction->target();
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function actionShippingCreateAction(string $salesChannelId, array $attributes = []): Shipping
    {
        $shippingCreateAction = new ShippingCreateAction(
            new Shipping(),
            array_merge([
                'sales_channel_id' => $salesChannelId,
                'enabled' => true,
                'name' => 'DHL',
                'country_code' => 'DE',
                'net_price' => 395,
                'gross_price' => 420,
                'currency_code' => 'EUR',
                'position' => 1,
            ], $attributes),
            TriggerEnum::App
        );

        $shippingCreateAction->trigger();

        return $shippingCreateAction->target();
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function actionTransactionCreate(
        string $salesChannelId,
        string $integrationId,
        string $integrationType,
        string $orderId,
        array $attributes = []
    ): Transaction {
        $transactionCreateActions = new TransactionCreateAction(
            new Transaction(),
            array_merge([
                'sales_channel_id' => $salesChannelId,
                'integration_id' => $integrationId,
                'integration_type' => $integrationType,
                'order_id' => $orderId,
                'status' => TransactionStatusEnum::Created,
                'method' => 'todo', // @todo
                'resource_id' => Str::uuid()->toString(),
                'resource_data' => [],
                'webhook_id' => Str::uuid()->toString(),
                'checkout_url' => 'https://checkout.de/resource-id',
            ], $attributes),
            TriggerEnum::App
        );

        $transactionCreateActions->trigger();

        return $transactionCreateActions->target();
    }

    // </editor-fold>

    // <editor-fold desc="Integration">

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function actionNeuronInventoryCreate(string $salesChannelId, array $attributes = []): NeuronInventory
    {
        $neuronInventoryCreateAction = new NeuronInventoryCreateAction(
            new NeuronInventory(),
            array_merge([
                'sales_channel_id' => $salesChannelId,
                'enabled' => true,
                'receive_inventory' => true,
                'distribute_order' => true,
                'name' => 'Neuron Inventory',
            ], $attributes),
            TriggerEnum::App
        );

        $neuronInventoryCreateAction->trigger();

        return $neuronInventoryCreateAction->target();
    }

    // </editor-fold>
}
