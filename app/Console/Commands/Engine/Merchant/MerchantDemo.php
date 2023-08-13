<?php

namespace App\Console\Commands\Engine\Merchant;

use App\Actions\Engine\ActionRule\ActionRuleCreateAction;
use App\Actions\Engine\CartRule\CartRuleCreateAction;
use App\Actions\Engine\Condition\ConditionCreateAction;
use App\Actions\Engine\Coupon\CouponCreateAction;
use App\Actions\Engine\Merchant\MerchantCreateAction;
use App\Actions\Engine\Order\OrderAddItemAction;
use App\Actions\Engine\Order\OrderUpdateItemQuantityAction;
use App\Actions\Engine\Payment\PaymentCreateAction;
use App\Actions\Engine\Product\ProductCreateAction;
use App\Actions\Engine\ProductPrice\ProductPriceCreateAction;
use App\Actions\Engine\Rule\RuleCreateAction;
use App\Actions\Engine\SalesChannel\SalesChannelCreateAction;
use App\Actions\Engine\SalesChannel\SalesChannelUpdateAction;
use App\Actions\Engine\Shipping\ShippingCreateAction;
use App\Actions\Engine\Vat\VatCreateAction;
use App\Actions\Integration\Inventory\Billbee\BillbeeCreateAction;
use App\Actions\Integration\Inventory\NeuronInventory\NeuronInventoryCreateAction;
use App\Actions\Integration\Inventory\Weclapp\WeclappCreateAction;
use App\Actions\Integration\Mail\Mailgun\MailgunCreateAction;
use App\Actions\Integration\Marketing\Klicktipp\KlicktippCreateAction;
use App\Actions\Integration\PaymentProvider\AmazonPay\AmazonPayCreateAction;
use App\Actions\Integration\PaymentProvider\Mollie\MollieCreateAction;
use App\Actions\Integration\PaymentProvider\NeuronPayment\NeuronPaymentCreateAction;
use App\Actions\Integration\PaymentProvider\Paypal\PaypalCreateAction;
use App\Actions\Integration\PaymentProvider\PostFinance\PostFinanceCreateAction;
use App\Condition\Presets\MaxActionProductQuantity;
use App\Condition\Presets\NewCustomer;
use App\Condition\Presets\OrderValueIsGreaterOrEqualToAmount;
use App\Consequence\Presets\AddFreeItem;
use App\Consequence\Presets\FreeShipping;
use App\Consequence\Presets\PercentageDiscountOnAllProducts;
use App\Console\Commands\Traits\MerchantSetup;
use App\Enums\Payment\PaymentMethodEnum;
use App\Enums\Product\ProductTypeEnum;
use App\Exceptions\Order\PolicyException;
use App\Facades\SalesChannel;
use App\Facades\Stock;
use App\Models\Engine\ActionRule;
use App\Models\Engine\CartRule;
use App\Models\Engine\Condition;
use App\Models\Engine\Coupon;
use App\Models\Engine\Merchant;
use App\Models\Engine\Payment;
use App\Models\Engine\Product;
use App\Models\Engine\ProductPrice;
use App\Models\Engine\Rule;
use App\Models\Engine\Shipping;
use App\Models\Engine\Vat;
use App\Models\Integration\Inventory\Billbee;
use App\Models\Integration\Inventory\NeuronInventory;
use App\Models\Integration\Inventory\Weclapp;
use App\Models\Integration\Mail\Mailgun;
use App\Models\Integration\Marketing\Klicktipp;
use App\Models\Integration\PaymentProvider\AmazonPay;
use App\Models\Integration\PaymentProvider\Mollie;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use App\Models\Integration\PaymentProvider\Paypal;
use App\Models\Integration\PaymentProvider\PostFinance;
use App\Models\User;
use App\Product\Configuration\BundleConfiguration;
use App\Product\Configuration\BundleConfigurationGroup;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MerchantDemo extends Command
{
    use MerchantSetup;

    protected $signature = 'merchant:demo';

    protected $description = 'Setup a demo merchant';

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function handle(): int
    {
        if (App::environment('production')) {
            return Command::FAILURE;
        }

        $this->merchantSetupInit();

        \Laravel\Prompts\intro('User');

        $user = $this->setupUser();

        \Laravel\Prompts\intro('Creating resources');

        $merchant = $this->setupMerchant('Neuron Merchant');
        $user->merchant()->associate($merchant)->save();
        $salesChannel1 = $this->setupSalesChannel(
            $merchant,
            'Webshop Germany',
            'EUR',
            'de_DE',
            [
                MollieCreateAction::class,
                AmazonPayCreateAction::class,
                WeclappCreateAction::class,
                PaypalCreateAction::class
            ]
        );
        $salesChannel2 = $this->setupSalesChannel(
            $merchant,
            'Webshop Swiss',
            'CHF',
            'de_CH',
            [
                PostFinanceCreateAction::class
            ]
        );

        SalesChannel::set($salesChannel1);

        $this->prepareStoreProject($salesChannel1);
        $this->merchantSetupSummary();

        return Command::SUCCESS;
    }

    public function setupUser(): User
    {
        $password = 'password';

        /** @var User $user */
        $user = User::create([
            'name' => 'David',
            'email' => 'david@brainspin.de',
            'password' => Hash::make($password),
        ]);

        $user->markEmailAsVerified();

        $token = $user->createToken('this-is-a-token');

        \Laravel\Prompts\info('User:     ' . $user->name);
        \Laravel\Prompts\info('Email:    ' . $user->email);
        \Laravel\Prompts\info('Password: ' . $password);

        return $user;
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     */
    public function setupMerchant(string $name): Merchant
    {
        return $this->merchantSetupAction(MerchantCreateAction::class, new Merchant(), [
            'name' => $name
        ]);
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    public function setupSalesChannel(
        Merchant $merchant,
        string $name,
        string $currencyCode,
        string $locale,
        array $allowedActions
    ): \App\Models\Engine\SalesChannel {
        $createProducts = true;

        $salesChannel = $this->merchantSetupAction(SalesChannelCreateAction::class, new \App\Models\Engine\SalesChannel(), [
            'merchant_id' => $merchant->id,
            'name' => $name,
            'currency_code' => $currencyCode,
            'locale' => $locale,
            'domains' => ['neuron.ddev.site'],
            'use_stock' => true,
            'remove_items_on_price_increase' => true,
            'checkout_summary_url' => route('shop.checkout', ['id' => 'replace-with-uuid']) . '?summary',
            'order_summary_url' => route('shop.checkout', ['id' => 'replace-with-uuid']) . '?order-summary',
        ]);

        $salesChannel = $this->merchantSetupAction(SalesChannelUpdateAction::class, $salesChannel, [
            'checkout_summary_url' => route('shop.checkout', [
                    'id' => $salesChannel->id
                ]) . '#confirmation',
            'order_summary_url' => route('shop.order', [
                    'id' => $salesChannel->id,
                    'orderId' => 'orderId',
                    'orderNumber' => 'orderNumber',
                ])
        ]);

        SalesChannel::set($salesChannel);

        $neuronInventory = $this->merchantSetupAction(NeuronInventoryCreateAction::class, new NeuronInventory(), [
            'sales_channel_id' => $salesChannel->id,
            'enabled' => true,
            'receive_inventory' => true,
            'distribute_order' => true,
            'name' => 'Neuron Inventory',
        ]);

        $mainInventory = $neuronInventory;

        if (in_array(WeclappCreateAction::class, $allowedActions) &&
            env('MERCHANT_DEMO_WECLAPP_API_TOKEN') &&
            env('MERCHANT_DEMO_WECLAPP_URL')
        ) {
            $weclapp = $this->merchantSetupAction(WeclappCreateAction::class, new Weclapp(), [
                'sales_channel_id' => $salesChannel->id,
                'enabled' => false,
                'receive_inventory' => true,
                'distribute_order' => true,
                'name' => 'Weclapp',
                'url' => env('MERCHANT_DEMO_WECLAPP_URL'),
                'api_token' => env('MERCHANT_DEMO_WECLAPP_API_TOKEN'),
                'article_category_id' => '4317',
                'distribution_channel' => 'GROSS1'
            ]);

            if ($weclapp->enabled) {
                $createProducts = false;
            }
        }

        $neuronPayment = $this->merchantSetupAction(NeuronPaymentCreateAction::class, new NeuronPayment(), [
            'sales_channel_id' => $salesChannel->id,
            'enabled' => true,
            'name' => 'Neuron Payment',
        ]);

        if (in_array(MollieCreateAction::class, $allowedActions)) {
            if (env('MERCHANT_DEMO_MOLLIE_API_KEY') &&
                env('MERCHANT_DEMO_MOLLIE_PROFILE_ID')
            ) {
                $mollie = $this->merchantSetupAction(MollieCreateAction::class, new Mollie(), [
                    'sales_channel_id' => $salesChannel->id,
                    'enabled' => true,
                    'name' => 'Mollie',
                    'api_key' => env('MERCHANT_DEMO_MOLLIE_API_KEY'),
                    'profile_id' => env('MERCHANT_DEMO_MOLLIE_PROFILE_ID'),
                ]);

                $payment4 = $this->merchantSetupAction(PaymentCreateAction::class, new Payment(), [
                    'sales_channel_id' => $salesChannel->id,
                    'integration_id' => $mollie->id,
                    'integration_type' => Mollie::class,
                    'enabled' => true,
                    'name' => 'Creditcard',
                    'method' => PaymentMethodEnum::Creditcard->value,
                    'position' => 4,
                    'description' => 'Pay with Creditcard'
                ]);

                $payment6 = $this->merchantSetupAction(PaymentCreateAction::class, new Payment(), [
                    'sales_channel_id' => $salesChannel->id,
                    'integration_id' => $mollie->id,
                    'integration_type' => Mollie::class,
                    'enabled' => true,
                    'name' => 'PayPal',
                    'method' => PaymentMethodEnum::Paypal->value,
                    'position' => 6,
                    'description' => 'Pay with Paypal'
                ]);

                $payment4 = $this->merchantSetupAction(PaymentCreateAction::class, new Payment(), [
                    'sales_channel_id' => $salesChannel->id,
                    'integration_id' => $mollie->id,
                    'integration_type' => Mollie::class,
                    'enabled' => true,
                    'name' => 'Giropay',
                    'method' => PaymentMethodEnum::Giropay->value,
                    'position' => 7,
                    'description' => 'Pay with Giropay'
                ]);
            }
        }

        if (in_array(PaypalCreateAction::class, $allowedActions)) {
            if (env('MERCHANT_DEMO_PAYPAL_CLIENT_ID') &&
                env('MERCHANT_DEMO_PAYPAL_SECRET_KEY')
            ) {
                $paypal = $this->merchantSetupAction(PaypalCreateAction::class, new Paypal(), [
                    'sales_channel_id' => $salesChannel->id,
                    'enabled' => true,
                    'name' => 'Paypal',
                    'client_id' => env('MERCHANT_DEMO_PAYPAL_CLIENT_ID'),
                    'client_secret' => env('MERCHANT_DEMO_PAYPAL_SECRET_KEY'),
                ]);

                $payment7 = $this->merchantSetupAction(PaymentCreateAction::class, new Payment(), [
                    'sales_channel_id' => $salesChannel->id,
                    'integration_id' => $paypal->id,
                    'integration_type' => Paypal::class,
                    'enabled' => true,
                    'name' => 'Paypal',
                    'method' => PaymentMethodEnum::Proxy->value,
                    'position' => 7,
                    'description' => 'Pay with Paypal'
                ]);
            }
        }

        if (in_array(AmazonPayCreateAction::class, $allowedActions)) {
            if (env('MERCHANT_DEMO_AMAZON_PAY_PUBLIC_KEY_ID') &&
                env('MERCHANT_DEMO_AMAZON_PAY_PRIVATE_KEY') &&
                env('MERCHANT_DEMO_AMAZON_PAY_STORE_ID') &&
                env('MERCHANT_DEMO_AMAZON_PAY_MERCHANT_ACCOUNT_ID')
            ) {
                $amazonPay = $this->merchantSetupAction(AmazonPayCreateAction::class, new AmazonPay(), [
                    'sales_channel_id' => $salesChannel->id,
                    'enabled' => true,
                    'name' => 'AmazonPay',
                    'merchant_account_id' => env('MERCHANT_DEMO_AMAZON_PAY_MERCHANT_ACCOUNT_ID'),
                    'public_key_id' => env('MERCHANT_DEMO_AMAZON_PAY_PUBLIC_KEY_ID'),
                    'private_key' => env('MERCHANT_DEMO_AMAZON_PAY_PRIVATE_KEY'),
                    'region' => 'eu',
                    'store_id' => env('MERCHANT_DEMO_AMAZON_PAY_STORE_ID'),
                    'sandbox' => true,
                ]);

                $payment5 = $this->merchantSetupAction(PaymentCreateAction::class, new Payment(), [
                    'sales_channel_id' => $salesChannel->id,
                    'integration_id' => $amazonPay->id,
                    'integration_type' => AmazonPay::class,
                    'enabled' => true,
                    'name' => 'AmazonPay',
                    'method' => PaymentMethodEnum::Proxy->value,
                    'position' => 5,
                    'description' => 'Amazon Pay'
                ]);
            }
        }

        if (in_array(PostFinanceCreateAction::class, $allowedActions)) {
            if (env('MERCHANT_DEMO_POST_FINANCE_SPACE_ID') &&
                env('MERCHANT_DEMO_POST_FINANCE_USER_ID') &&
                env('MERCHANT_DEMO_POST_FINANCE_SECRET')
            ) {
                $postFinance = $this->merchantSetupAction(PostFinanceCreateAction::class, new PostFinance(), [
                    'sales_channel_id' => $salesChannel->id,
                    'enabled' => true,
                    'name' => 'PostFinance',
                    'space_id' => env('MERCHANT_DEMO_POST_FINANCE_SPACE_ID'),
                    'user_id' => env('MERCHANT_DEMO_POST_FINANCE_USER_ID'),
                    'secret' => env('MERCHANT_DEMO_POST_FINANCE_SECRET'),
                ]);
            }
        }


        if (env('MERCHANT_DEMO_MAILGUN_DOMAIN') &&
            env('MERCHANT_DEMO_MAILGUN_SECRET') &&
            env('MERCHANT_DEMO_MAILGUN_API_KEY')
        ) {
            $mailgun = $this->merchantSetupAction(MailgunCreateAction::class, new Mailgun(), [
                'sales_channel_id' => $salesChannel->id,
                'enabled' => true,
                'distribute_order' => true,
                'refund_order' => true,
                'name' => 'Mailgun',
                'domain' => env('MERCHANT_DEMO_MAILGUN_DOMAIN'),
                'endpoint' => 'api.mailgun.net',
                'secret' => env('MERCHANT_DEMO_MAILGUN_SECRET'),
                'api_key' => env('MERCHANT_DEMO_MAILGUN_API_KEY'),
                'order_template' => 'neuronorder',
                'refund_template' => 'neuronrefund',
                'from' => 'shop@neuron.de',
                'order_subject' => 'Order confirmation {order_number}',
                'refund_subject' => 'Refund {order_number}',
                'sandbox_to' => 'david@brainspin.de',
            ]);
        }

        if (env('MERCHANT_DEMO_BILLBEE_USER') &&
            env('MERCHANT_DEMO_BILLBEE_API_PASSWORD') &&
            env('MERCHANT_DEMO_BILLBEE_API_KEY') &&
            env('MERCHANT_DEMO_BILLBEE_SHOP_ID')
        ) {
            $billbee = $this->merchantSetupAction(BillbeeCreateAction::class, new Billbee(), [
                'sales_channel_id' => $salesChannel->id,
                'enabled' => false,
                'receive_inventory' => true,
                'distribute_order' => false,
                'name' => 'Billbee',
                'user' => env('MERCHANT_DEMO_BILLBEE_USER'),
                'api_password' => env('MERCHANT_DEMO_BILLBEE_API_PASSWORD'),
                'api_key' => env('MERCHANT_DEMO_BILLBEE_API_KEY'),
                'shop_id' => env('MERCHANT_DEMO_BILLBEE_SHOP_ID'),
            ]);

            if ($billbee->enabled) {
                $createProducts = false;
            }
        }

        if (env('MERCHANT_DEMO_KLICKTIPP_USER_NAME') &&
            env('MERCHANT_DEMO_KLICKTIPP_DEVELOPER_KEY') &&
            env('MERCHANT_DEMO_KLICKTIPP_CUSTOMER_KEY')
        ) {
            $klicktipp = $this->merchantSetupAction(KlicktippCreateAction::class, new Klicktipp(), [
                'sales_channel_id' => $salesChannel->id,
                'enabled' => true,
                'distribute_order' => true,
                'name' => 'Klicktipp',
                'tag_prefix' => 'NEURON_',
                'user_name' => env('MERCHANT_DEMO_KLICKTIPP_USER_NAME'),
                'developer_key' => env('MERCHANT_DEMO_KLICKTIPP_DEVELOPER_KEY'),
                'customer_key' => env('MERCHANT_DEMO_KLICKTIPP_CUSTOMER_KEY'),
                'service' => 'https://api.klicktipp.com',
                'tags' => [],
                'tags_coupons' => [],
                'tags_periods' => [],
                'tags_new_customer' => [],
                'tags_products' => [],
            ]);
        }

        $shipping1 = $this->merchantSetupAction(ShippingCreateAction::class, new Shipping(), [
            'sales_channel_id' => $salesChannel->id,
            'enabled' => true,
            'name' => 'DHL',
            'country_code' => 'DE',
            'net_price' => 395,
            'gross_price' => 420,
            'currency_code' => 'EUR',
            'position' => 1,
            'default' => true
        ]);

        $shipping2 = $this->merchantSetupAction(ShippingCreateAction::class, new Shipping(), [
            'sales_channel_id' => $salesChannel->id,
            'enabled' => true,
            'name' => 'DHL Express',
            'country_code' => 'DE',
            'net_price' => 595,
            'gross_price' => 600,
            'currency_code' => 'EUR',
            'position' => 2,
        ]);

        $payment1 = $this->merchantSetupAction(PaymentCreateAction::class, new Payment(), [
            'sales_channel_id' => $salesChannel->id,
            'integration_id' => $neuronPayment->id,
            'integration_type' => NeuronPayment::class,
            'enabled' => true,
            'name' => 'Prepayment',
            'method' => PaymentMethodEnum::Prepayment->value,
            'position' => 2,
            'description' => 'Order, pay, get delivery',
            'default' => true
        ]);

        $payment2 = $this->merchantSetupAction(PaymentCreateAction::class, new Payment(), [
            'sales_channel_id' => $salesChannel->id,
            'integration_id' => $neuronPayment->id,
            'integration_type' => NeuronPayment::class,
            'enabled' => true,
            'name' => 'Creditcard',
            'method' => PaymentMethodEnum::Creditcard->value,
            'position' => 3,
            'description' => 'Pay with Creditcard'
        ]);

        $payment3 = $this->merchantSetupAction(PaymentCreateAction::class, new Payment(), [
            'sales_channel_id' => $salesChannel->id,
            'integration_id' => $neuronPayment->id,
            'integration_type' => NeuronPayment::class,
            'enabled' => true,
            'name' => 'Free',
            'method' => PaymentMethodEnum::Free->value,
            'position' => 1,
            'description' => 'Method for free carts'
        ]);

        if ($createProducts) {
            $product1 = $this->merchantSetupAction(ProductCreateAction::class, new Product(), [
                'sales_channel_id' => $salesChannel->id,
                'inventoryable_type' => get_class($mainInventory),
                'inventoryable_id' => $mainInventory->id,
                'inventory_id' => Str::uuid()->toString(),
                'enabled' => true,
                'name' => 'Bonbon',
                'type' => ProductTypeEnum::Product->value,
                'sku' => 'bonbon',
                'net_price' => 395,
                'gross_price' => 400,
                'configuration' => null,
                'url' => url('/products/bonbon'),
                'image_url' => asset('/storage/products/bonbon.jpg'),
            ]);

            $productPrice1 = $this->merchantSetupAction(ProductPriceCreateAction::class, new ProductPrice(), [
                'product_id' => $product1->id,
                'net_price' => (int)($product1->net_price * 0.8),
                'gross_price' => (int)($product1->gross_price * 0.8),
                'begin_at' => now()->addWeek(),
                'end_at' => now()->addWeeks(2),
                'enabled' => true,
            ]);

            $product2 = $this->merchantSetupAction(ProductCreateAction::class, new Product(), [
                'sales_channel_id' => $salesChannel->id,
                'inventoryable_type' => get_class($mainInventory),
                'inventoryable_id' => $mainInventory->id,
                'inventory_id' => Str::uuid()->toString(),
                'enabled' => true,
                'name' => 'Coffee',
                'type' => ProductTypeEnum::Product->value,
                'sku' => 'coffee',
                'net_price' => 320,
                'gross_price' => 400,
                'configuration' => null,
                'url' => url('/products/coffee'),
                'image_url' => asset('/storage/products/coffee.jpg'),
            ]);

            $productPrice2 = $this->merchantSetupAction(ProductPriceCreateAction::class, new ProductPrice(), [
                'product_id' => $product2->id,
                'net_price' => (int)($product2->net_price * 0.9),
                'gross_price' => (int)($product2->gross_price * 0.9),
                'begin_at' => now(),
                'end_at' => now()->addHour(),
                'enabled' => true,
            ]);

            $productPrice3 = $this->merchantSetupAction(ProductPriceCreateAction::class, new ProductPrice(), [
                'product_id' => $product2->id,
                'net_price' => (int)($product2->net_price * 0.7),
                'gross_price' => (int)($product2->gross_price * 0.7),
                'begin_at' => now()->addWeeks(2),
                'end_at' => now()->addWeeks(3),
                'enabled' => true,
            ]);

            $product3 = $this->merchantSetupAction(ProductCreateAction::class, new Product(), [
                'sales_channel_id' => $salesChannel->id,
                'inventoryable_type' => get_class($mainInventory),
                'inventoryable_id' => $mainInventory->id,
                'inventory_id' => Str::uuid()->toString(),
                'enabled' => true,
                'name' => 'Cupcake',
                'type' => ProductTypeEnum::Product->value,
                'sku' => 'cupcake',
                'net_price' => 500,
                'gross_price' => 600,
                'configuration' => null,
                'url' => url('/products/cupcake'),
                'image_url' => asset('/storage/products/cupcake.jpg'),
            ]);

            $product4 = $this->merchantSetupAction(ProductCreateAction::class, new Product(), [
                'sales_channel_id' => $salesChannel->id,
                'inventoryable_type' => get_class($mainInventory),
                'inventoryable_id' => $mainInventory->id,
                'inventory_id' => Str::uuid()->toString(),
                'enabled' => true,
                'name' => 'Potion',
                'type' => ProductTypeEnum::Product->value,
                'sku' => 'potion',
                'net_price' => 9998,
                'gross_price' => 11000,
                'configuration' => null,
                'url' => url('/products/potion'),
                'image_url' => asset('/storage/products/potion.jpg'),
            ]);

            $product5 = $this->merchantSetupAction(ProductCreateAction::class, new Product(), [
                'sales_channel_id' => $salesChannel->id,
                'inventoryable_type' => get_class($mainInventory),
                'inventoryable_id' => $mainInventory->id,
                'inventory_id' => Str::uuid()->toString(),
                'enabled' => true,
                'name' => 'Power Bundle',
                'type' => ProductTypeEnum::Bundle->value,
                'sku' => 'power',
                'net_price' => 19998,
                'gross_price' => 21000,
                'configuration' => BundleConfiguration::make()
                    ->addGroup(
                        BundleConfigurationGroup::make()
                            ->addProduct($product1)
                    )
                    ->addGroup(
                        BundleConfigurationGroup::make()
                            ->addProduct($product2)
                            ->addProduct($product3)
                    )
                    ->addGroup(
                        BundleConfigurationGroup::make()
                            ->addProduct($product2)
                            ->addProduct($product3)
                    )->toArray(),
                'url' => url('/products/powerbundle'),
                'image_url' => asset('/storage/products/powerbundle.jpg'),
            ]);

            Stock::add($product1->id, 83);
            Stock::add($product2->id, 45);
            Stock::add($product3->id, 11);
            Stock::add($product4->id, 73);

            $actionRule1 = $this->action_rule_max_10_product_quantity_on_cart_add_item($salesChannel, $product1);
            $actionRule2 = $this->action_rule_max_10_product_quantity_on_cart_update_item($salesChannel, $product1);
            $coupon3 = $this->coupon_add_free_item_if_cart_value_is_greater_than_20($salesChannel, $product1);

            $countryCodes = Shipping::where('sales_channel_id', $salesChannel->id)
                ->pluck('country_code');
            $productIds = Product::where('sales_channel_id', $salesChannel->id)
                ->pluck('id');

            $vatRates = [
                0,
                700,
                1600,
                1900,
                2100,
            ];

            foreach ($countryCodes as $countryCode) {
                foreach ($productIds as $productId) {
                    $vat = $this->merchantSetupAction(VatCreateAction::class, new Vat(), [
                        'sales_channel_id' => $salesChannel->id,
                        'vatable_type' => Product::class,
                        'vatable_id' => $productId,
                        'country_code' => $countryCode,
                        'rate' => $vatRates[random_int(0, count($vatRates) - 1)]
                    ]);
                }
            }
        }

        $coupon1 = $this->coupon_10_percent_discount_if_cart_value_is_greater_than_10($salesChannel);
        $coupon2 = $this->coupon_10_percent_discount_for_new_customer($salesChannel);
        $coupon4 = $this->coupon_free_shipping_if_cart_value_is_greater_than_10($salesChannel);
        $cartRule1 = $this->cart_rule_10_percent_discount_if_cart_value_is_greater_than_10($salesChannel);

        $countryCodes = Shipping::where('sales_channel_id', $salesChannel->id)
            ->pluck('country_code');
        $shippingIds = Shipping::where('sales_channel_id', $salesChannel->id)
            ->pluck('id');

        $vatRates = [
            0,
            700,
            1600,
            1900,
            2100,
        ];

        foreach ($countryCodes as $countryCode) {
            foreach ($shippingIds as $shippingId) {
                $vat = $this->merchantSetupAction(VatCreateAction::class, new Vat(), [
                    'sales_channel_id' => $salesChannel->id,
                    'vatable_type' => Shipping::class,
                    'vatable_id' => $shippingId,
                    'country_code' => $countryCode,
                    'rate' => $vatRates[random_int(0, count($vatRates) - 1)]
                ]);
            }
        }

        return $salesChannel;
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     */
    protected function coupon_10_percent_discount_if_cart_value_is_greater_than_10(\App\Models\Engine\SalesChannel $salesChannel): Coupon
    {
        $condition = $this->merchantSetupAction(ConditionCreateAction::class, new Condition(), [
            'sales_channel_id' => $salesChannel->id,
            'name' => OrderValueIsGreaterOrEqualToAmount::name('10 ' . $salesChannel->currency_code),
            'collection' => OrderValueIsGreaterOrEqualToAmount::make(1000)->toArray()
        ]);

        $rule = $this->merchantSetupAction(RuleCreateAction::class, new Rule(), [
            'sales_channel_id' => $salesChannel->id,
            'condition_id' => $condition->id,
            'name' => PercentageDiscountOnAllProducts::name("10 %"),
            'consequences' => PercentageDiscountOnAllProducts::make(10)->toArray(),
            'position' => 1,
        ]);

        return $this->merchantSetupAction(CouponCreateAction::class, new Coupon(), [
            'sales_channel_id' => $salesChannel->id,
            'rule_id' => $rule->id,
            'name' => '10% for everybody',
            'code' => 'TENPERCENT',
            'enabled' => true,
            'combinable' => false,
        ]);
    }


    /**
     * @throws PolicyException
     * @throws ValidationException
     */
    protected function coupon_10_percent_discount_for_new_customer(\App\Models\Engine\SalesChannel $salesChannel): Coupon
    {
        $condition = $this->merchantSetupAction(ConditionCreateAction::class, new Condition(), [
            'sales_channel_id' => $salesChannel->id,
            'name' => NewCustomer::name(),
            'collection' => NewCustomer::make()->toArray()
        ]);

        $rule = $this->merchantSetupAction(RuleCreateAction::class, new Rule(), [
            'sales_channel_id' => $salesChannel->id,
            'condition_id' => $condition->id,
            'name' => '10% for new customers',
            'consequences' => PercentageDiscountOnAllProducts::make(10)->toArray(),
            'position' => 1,
        ]);

        return $this->merchantSetupAction(CouponCreateAction::class, new Coupon(), [
            'sales_channel_id' => $salesChannel->id,
            'rule_id' => $rule->id,
            'name' => '10% for new customers',
            'code' => 'TENFORNEW',
            'enabled' => true,
            'combinable' => true
        ]);
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    protected function coupon_add_free_item_if_cart_value_is_greater_than_20(\App\Models\Engine\SalesChannel $salesChannel, Product $product): Coupon
    {
        $condition = $this->merchantSetupAction(ConditionCreateAction::class, new Condition(), [
            'sales_channel_id' => $salesChannel->id,
            'name' => OrderValueIsGreaterOrEqualToAmount::name('20 ' . $salesChannel->currency_code),
            'collection' => OrderValueIsGreaterOrEqualToAmount::make(2000)->toArray()
        ]);

        $rule = $this->merchantSetupAction(RuleCreateAction::class, new Rule(), [
            'sales_channel_id' => $salesChannel->id,
            'condition_id' => $condition->id,
            'name' => 'Free ' . $product->name . ' if cart value > 20' . $salesChannel->currency_code,
            'consequences' => AddFreeItem::make($product->id, 1, null)->toArray(),
            'position' => 1,
        ]);

        return $this->merchantSetupAction(CouponCreateAction::class, new Coupon(), [
            'sales_channel_id' => $salesChannel->id,
            'rule_id' => $rule->id,
            'name' => 'Free ' . $product->name . ' if cart value > 20' . $salesChannel->currency_code,
            'code' => 'FREEPRODUCT',
            'enabled' => true,
            'combinable' => false
        ]);
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    protected function coupon_free_shipping_if_cart_value_is_greater_than_10(\App\Models\Engine\SalesChannel $salesChannel): Coupon
    {
        $condition = $this->merchantSetupAction(ConditionCreateAction::class, new Condition(), [
            'sales_channel_id' => $salesChannel->id,
            'name' => OrderValueIsGreaterOrEqualToAmount::name('10 ' . $salesChannel->currency_code),
            'collection' => OrderValueIsGreaterOrEqualToAmount::make(1000)->toArray()
        ]);

        $rule = $this->merchantSetupAction(RuleCreateAction::class, new Rule(), [
            'sales_channel_id' => $salesChannel->id,
            'condition_id' => $condition->id,
            'name' => 'Free shipping if cart > 10' . $salesChannel->currency_code,
            'consequences' => FreeShipping::make()->toArray(),
            'position' => 1,
        ]);

        return $this->merchantSetupAction(CouponCreateAction::class, new Coupon(), [
            'sales_channel_id' => $salesChannel->id,
            'rule_id' => $rule->id,
            'name' => 'Free shipping',
            'code' => 'FREESHIPPING',
            'enabled' => true,
            'combinable' => false
        ]);
    }

    /**
     * @throws PolicyException
     * @throws ValidationException
     */
    protected function cart_rule_10_percent_discount_if_cart_value_is_greater_than_10(\App\Models\Engine\SalesChannel $salesChannel): CartRule
    {
        $condition = $this->merchantSetupAction(ConditionCreateAction::class, new Condition(), [
            'sales_channel_id' => $salesChannel->id,
            'name' => OrderValueIsGreaterOrEqualToAmount::name('10 ' . $salesChannel->currency_code),
            'collection' => OrderValueIsGreaterOrEqualToAmount::make(1000)->toArray()
        ]);

        $rule = $this->merchantSetupAction(RuleCreateAction::class, new Rule(), [
            'sales_channel_id' => $salesChannel->id,
            'condition_id' => $condition->id,
            'name' => PercentageDiscountOnAllProducts::name("10 %"),
            'consequences' => PercentageDiscountOnAllProducts::make(10)->toArray(),
            'position' => 1,
        ]);

        return $this->merchantSetupAction(CartRuleCreateAction::class, new CartRule(), [
            'sales_channel_id' => $salesChannel->id,
            'rule_id' => $rule->id,
            'name' => 'Winter Sale',
            'enabled' => true,
        ]);
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    protected function action_rule_max_10_product_quantity_on_cart_add_item(\App\Models\Engine\SalesChannel $salesChannel, Product $product): ActionRule
    {
        $condition = $this->merchantSetupAction(ConditionCreateAction::class, new Condition(), [
            'sales_channel_id' => $salesChannel->id,
            'name' => MaxActionProductQuantity::name($product->name, 10),
            'collection' => MaxActionProductQuantity::make($product->id, 10)->toArray()
        ]);

        return $this->merchantSetupAction(ActionRuleCreateAction::class, new ActionRule(), [
            'sales_channel_id' => $salesChannel->id,
            'condition_id' => $condition->id,
            'name' => 'Max 10 ' . $product->name,
            'action' => class_basename(OrderAddItemAction::class),
            'enabled' => true,
        ]);
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    protected function action_rule_max_10_product_quantity_on_cart_update_item(\App\Models\Engine\SalesChannel $salesChannel, Product $product): ActionRule
    {
        $condition = $this->merchantSetupAction(ConditionCreateAction::class, new Condition(), [
            'sales_channel_id' => $salesChannel->id,
            'name' => MaxActionProductQuantity::name($product->name, 10),
            'collection' => MaxActionProductQuantity::make($product->id, 10)->toArray()
        ]);

        return $this->merchantSetupAction(ActionRuleCreateAction::class, new ActionRule(), [
            'sales_channel_id' => $salesChannel->id,
            'condition_id' => $condition->id,
            'name' => 'Max 10 ' . $product->name,
            'action' => class_basename(OrderUpdateItemQuantityAction::class),
            'enabled' => true,
        ]);
    }
}
