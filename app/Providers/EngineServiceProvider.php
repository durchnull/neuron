<?php

namespace App\Providers;

use App\Actions\Engine\Order\OrderAddCartRuleAction;
use App\Actions\Engine\Order\OrderAddItemAction;
use App\Actions\Engine\Order\OrderCancelAction;
use App\Actions\Engine\Order\OrderCreateAction;
use App\Actions\Engine\Order\OrderDeleteAction;
use App\Actions\Engine\Order\OrderPlaceAction;
use App\Actions\Engine\Order\OrderRedeemCouponAction;
use App\Actions\Engine\Order\OrderRefundAction;
use App\Actions\Engine\Order\OrderRemoveCartRuleAction;
use App\Actions\Engine\Order\OrderRemoveCouponAction;
use App\Actions\Engine\Order\OrderRemoveItemAction;
use App\Actions\Engine\Order\OrderShipAction;
use App\Actions\Engine\Order\OrderUpdateCustomerAction;
use App\Actions\Engine\Order\OrderUpdateItemAction;
use App\Actions\Engine\Order\OrderUpdateItemQuantityAction;
use App\Actions\Engine\Order\OrderUpdatePaymentAction;
use App\Actions\Engine\Order\OrderUpdateShippingAction;
use App\Actions\Engine\Order\OrderUpdateStatusAction;
use App\Enums\Order\OrderStatusEnum;
use App\Enums\Generator\StringPattern;
use App\Generators\CouponCodeGenerator;
use App\Generators\OrderNumberGenerator;
use App\Services\Engine\OrderService;
use App\Services\Engine\RuleService;
use App\Services\Engine\CouponService;
use App\Services\Engine\CustomerService;
use App\Services\Engine\InfiniteStockService;
use App\Services\Engine\MerchantService;
use App\Services\Engine\SalesChannelService;
use App\Services\Engine\ShippingService;
use App\Services\Engine\StockService;
use App\Services\Engine\TransactionService;
use App\Services\Engine\VatService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class EngineServiceProvider extends ServiceProvider
{
    protected array $statusFlow = [
        OrderStatusEnum::Open->value => [
            OrderStatusEnum::Placing->value,
            OrderStatusEnum::Canceled->value
        ],
        OrderStatusEnum::Placing->value => [
            OrderStatusEnum::Open->value,
            OrderStatusEnum::Accepted->value,
            OrderStatusEnum::Confirmed->value,
        ],
        OrderStatusEnum::Accepted->value => [
            OrderStatusEnum::Confirmed->value,
            OrderStatusEnum::Canceled->value,
        ],
        OrderStatusEnum::Confirmed->value => [
            OrderStatusEnum::Shipped->value,
            OrderStatusEnum::Refunded->value,
        ],
        OrderStatusEnum::Canceled->value => [
        ]
    ];

    protected array $actionFlow = [
        OrderStatusEnum::Open->value => [
            OrderCreateAction::class,
            OrderAddItemAction::class,
            OrderRemoveItemAction::class,
            OrderUpdateItemAction::class,
            OrderUpdateItemQuantityAction::class,
            OrderUpdateShippingAction::class,
            OrderUpdatePaymentAction::class,
            OrderUpdateCustomerAction::class,
            OrderAddCartRuleAction::class,
            OrderRemoveCartRuleAction::class,
            OrderRedeemCouponAction::class,
            OrderRemoveCouponAction::class,
            OrderPlaceAction::class,
            OrderCancelAction::class,
            OrderDeleteAction::class,
        ],
        OrderStatusEnum::Placing->value => [
            OrderUpdateStatusAction::class,
        ],
        OrderStatusEnum::Accepted->value => [
            OrderUpdateStatusAction::class,
            OrderCancelAction::class
        ],
        OrderStatusEnum::Confirmed->value => [
            OrderUpdateStatusAction::class,
            OrderRefundAction::class,
            OrderShipAction::class,
        ],
        OrderStatusEnum::Shipped->value => [
            OrderUpdateStatusAction::class,
            OrderRefundAction::class
        ],
        OrderStatusEnum::Refunded->value => [
            OrderUpdateStatusAction::class,
        ],
        OrderStatusEnum::Canceled->value => [
            OrderDeleteAction::class,
        ]
    ];

    protected array $allowedActionRuleClasses = [
        OrderAddItemAction::class,
        OrderRedeemCouponAction::class,
        OrderRemoveCouponAction::class,
        OrderUpdateCustomerAction::class,
        OrderUpdateItemAction::class,
        OrderUpdateItemQuantityAction::class,
        OrderUpdatePaymentAction::class,
        OrderUpdateShippingAction::class,
    ];

    public function register(): void
    {
        $this->registerGenerators();
        $this->registerEngine();
    }

    protected function registerGenerators(): void
    {
        $this->app->singleton('coupon-code-generator', function (Application $app) {
            return new CouponCodeGenerator(StringPattern::NineAlphaNumericUpper);
        });


        $this->app->singleton('order-id-generator', function (Application $app) {
            return new OrderNumberGenerator(StringPattern::YearDashesNineAlphaNumericUpper);
        });
    }

    protected function registerEngine(): void
    {
        $this->app->singleton('merchant', function (Application $app) {
            return new MerchantService();
        });

        $this->app->singleton('sales-channel', function (Application $app) {
            return new SalesChannelService();
        });

        $this->app->singleton('shipping', function (Application $app) {
            return new ShippingService(
                $app->make('sales-channel')
            );
        });

        $this->app->singleton('stock', function (Application $app) {
            return $app->make('sales-channel')->get()->use_stock
                ? new StockService()
                : new InfiniteStockService();
        });

        $this->app->singleton('customer', function (Application $app) {
            return new CustomerService(
                $app->make('sales-channel')
            );
        });

        $this->app->singleton('vat', function (Application $app) {
            return new VatService(
                $app->make('sales-channel')
            );
        });

        $this->app->singleton('transaction', function (Application $app) {
            return new TransactionService(
                $app->make('integrations')
            );
        });

        $this->app->singleton('coupon', function (Application $app) {
            return new CouponService(
                $app->make('sales-channel'),
                $app->make('coupon-code-generator')
            );
        });

        $this->app->singleton('order', function (Application $app) {
            return new OrderService(
                $app->make('sales-channel'),
                $app->make('order-id-generator'),
                $this->statusFlow,
                $this->actionFlow,
            );
        });

        $this->app->singleton('rule', function (Application $app) {
            return new RuleService(
                $app->make('sales-channel'),
                $app->make('order'),
                $app->make('stock'),
                $this->allowedActionRuleClasses
            );
        });
    }

    public function boot(): void
    {
    }
}
