<?php

use App\Http\Controllers\Engine\ActionRuleController;
use App\Http\Controllers\Engine\AddressController;
use App\Http\Controllers\Engine\CartRuleController;
use App\Http\Controllers\Engine\ConditionController;
use App\Http\Controllers\Engine\CouponController;
use App\Http\Controllers\Engine\CustomerController;
use App\Http\Controllers\Engine\OrderController;
use App\Http\Controllers\Engine\PaymentController;
use App\Http\Controllers\Engine\ProductController;
use App\Http\Controllers\Engine\ProductPriceController;
use App\Http\Controllers\Engine\RuleController;
use App\Http\Controllers\Engine\SalesChannelController;
use App\Http\Controllers\Engine\ShippingController;
use App\Http\Controllers\Engine\StockController;
use App\Http\Controllers\Engine\TransactionController;
use App\Http\Controllers\Engine\VatController;
use App\Http\Controllers\Integration\Inventory\NeuronInventoryController;
use App\Http\Controllers\Integration\PaymentProvider\NeuronPaymentController;
use App\Http\Middleware\AuthenticateMerchant;
use App\Http\Middleware\AuthenticateSalesChannel;
use Illuminate\Support\Facades\Route;

Route::middleware(AuthenticateMerchant::class)->group(function () {
    Route::prefix('sales-channel')->group(function () {
        Route::post('/create', [SalesChannelController::class, 'create'])->name('api.sales-channel.create');
        Route::post('/update', [SalesChannelController::class, 'update'])->name('api.sales-channel.update');
        Route::delete('/delete', [SalesChannelController::class, 'delete'])->name('api.sales-channel.delete');
    });
});

Route::middleware(AuthenticateSalesChannel::class)->group(function () {
    Route::prefix('action-rule')->group(function () {
        Route::post('/', [ActionRuleController::class, 'show'])->name('api.action-rule.show');
        Route::post('/index', [ActionRuleController::class, 'index'])->name('api.action-rule.index');
        Route::post('/create', [ActionRuleController::class, 'create'])->name('api.action-rule.create');
        Route::post('/update', [ActionRuleController::class, 'update'])->name('api.action-rule.update');
        Route::delete('/delete', [ActionRuleController::class, 'delete'])->name('api.action-rule.delete');
    });

    Route::prefix('address')->group(function () {
        Route::post('/', [AddressController::class, 'show'])->name('api.address.show');
        Route::post('/index', [AddressController::class, 'index'])->name('api.address.index');
        Route::post('/create', [AddressController::class, 'create'])->name('api.address.create');
        Route::post('/update', [AddressController::class, 'update'])->name('api.address.update');
        Route::delete('/delete', [AddressController::class, 'delete'])->name('api.address.delete');
    });

    Route::prefix('cart-rule')->group(function () {
        Route::post('/', [CartRuleController::class, 'show'])->name('api.cart-rule.show');
        Route::post('/index', [CartRuleController::class, 'index'])->name('api.cart-rule.index');
        Route::post('/create', [CartRuleController::class, 'create'])->name('api.cart-rule.create');
        Route::post('/update', [CartRuleController::class, 'update'])->name('api.cart-rule.update');
        Route::delete('/delete', [CartRuleController::class, 'delete'])->name('api.cart-rule.delete');
    });

    Route::prefix('condition')->group(function () {
        Route::post('/', [ConditionController::class, 'show'])->name('api.condition.show');
        Route::post('/index', [ConditionController::class, 'index'])->name('api.condition.index');
        Route::post('/create', [ConditionController::class, 'create'])->name('api.condition.create');
        Route::post('/update', [ConditionController::class, 'update'])->name('api.condition.update');
        Route::delete('/delete', [ConditionController::class, 'delete'])->name('api.condition.delete');
    });

    Route::prefix('coupon')->group(function () {
        Route::post('/', [CouponController::class, 'show'])->name('api.coupon.show');
        Route::post('/index', [CouponController::class, 'index'])->name('api.coupon.index');
        Route::post('/create', [CouponController::class, 'create'])->name('api.coupon.create');
        Route::post('/update', [CouponController::class, 'update'])->name('api.coupon.update');
        Route::delete('/delete', [CouponController::class, 'delete'])->name('api.coupon.delete');
    });

    Route::prefix('customer')->group(function () {
        Route::post('/', [CustomerController::class, 'show'])->name('api.customer.show');
        Route::post('/index', [CustomerController::class, 'index'])->name('api.customer.index');
        Route::post('/create', [CustomerController::class, 'create'])->name('api.customer.create');
        Route::post('/update', [CustomerController::class, 'update'])->name('api.customer.update');
        Route::delete('/delete', [CustomerController::class, 'delete'])->name('api.customer.delete');
    });

    Route::prefix('order')->group(function () {
        Route::post('/', [OrderController::class, 'show'])->name('api.order.show');
        Route::post('/index', [OrderController::class, 'index'])->name('api.order.index');
        Route::delete('/delete', [OrderController::class, 'delete'])->name('api.order.delete');
        Route::post('/refund', [OrderController::class, 'refund'])->name('api.order.refund');
        Route::post('/cancel', [OrderController::class, 'cancel'])->name('api.order.cancel');
    });

    Route::prefix('payment')->group(function () {
        Route::post('/', [PaymentController::class, 'show'])->name('api.payment.show');
        Route::post('/index', [PaymentController::class, 'index'])->name('api.payment.index');
        Route::post('/create', [PaymentController::class, 'create'])->name('api.payment.create');
        Route::post('/update', [PaymentController::class, 'update'])->name('api.payment.update');
        Route::delete('/delete', [PaymentController::class, 'delete'])->name('api.payment.delete');
    });

    Route::prefix('product')->group(function () {
        Route::post('/', [ProductController::class, 'show'])->name('api.product.show');
        Route::post('/index', [ProductController::class, 'index'])->name('api.product.index');
        Route::post('/create', [ProductController::class, 'create'])->name('api.product.create');
        Route::post('/update', [ProductController::class, 'update'])->name('api.product.update');
        Route::delete('/delete', [ProductController::class, 'delete'])->name('api.product.delete');
    });

    Route::prefix('product-price')->group(function () {
        Route::post('/', [ProductPriceController::class, 'show'])->name('api.product-price.show');
        Route::post('/index', [ProductPriceController::class, 'index'])->name('api.product-price.index');
        Route::post('/create', [ProductPriceController::class, 'create'])->name('api.product-price.create');
        Route::post('/update', [ProductPriceController::class, 'update'])->name('api.product-price.update');
        Route::delete('/delete', [ProductPriceController::class, 'delete'])->name('api.product-price.delete');
    });

    Route::prefix('rule')->group(function () {
        Route::post('/', [RuleController::class, 'show'])->name('api.rule.show');
        Route::post('/index', [RuleController::class, 'index'])->name('api.rule.index');
        Route::post('/create', [RuleController::class, 'create'])->name('api.rule.create');
        Route::post('/update', [RuleController::class, 'update'])->name('api.rule.update');
        Route::delete('/delete', [RuleController::class, 'delete'])->name('api.rule.delete');
    });

    Route::prefix('shipping')->group(function () {
        Route::post('/', [ShippingController::class, 'show'])->name('api.shipping.show');
        Route::post('/index', [ShippingController::class, 'index'])->name('api.shipping.index');
        Route::post('/create', [ShippingController::class, 'create'])->name('api.shipping.create');
        Route::post('/update', [ShippingController::class, 'update'])->name('api.shipping.update');
        Route::delete('/delete', [ShippingController::class, 'delete'])->name('api.shipping.delete');
    });

    Route::prefix('stock')->group(function () {
        Route::post('/', [StockController::class, 'show'])->name('api.stock.show');
        Route::post('/index', [StockController::class, 'index'])->name('api.stock.index');
    });

    Route::prefix('transaction')->group(function () {
        Route::post('/', [TransactionController::class, 'show'])->name('api.transaction.show');
        Route::post('/index', [TransactionController::class, 'index'])->name('api.transaction.index');
    });

    Route::prefix('vat')->group(function () {
        Route::post('/', [VatController::class, 'show'])->name('api.vat.show');
        Route::post('/index', [VatController::class, 'index'])->name('api.vat.index');
        Route::post('/create', [VatController::class, 'create'])->name('api.vat.create');
        Route::post('/update', [VatController::class, 'update'])->name('api.vat.update');
        Route::delete('/delete', [VatController::class, 'delete'])->name('api.vat.delete');
    });

    Route::prefix('integration')->group(function () {
        Route::prefix('inventory')->group(function () {
            Route::prefix('neuron-inventory')->group(function () {
                Route::post('/create', [NeuronInventoryController::class, 'create'])->name('api.neuron-inventory.create');
                Route::post('/update', [NeuronInventoryController::class, 'update'])->name('api.neuron-inventory.update');
                Route::delete('/delete', [NeuronInventoryController::class, 'delete'])->name('api.neuron-inventory.delete');
            });
        });

        Route::prefix('payment-provider')->group(function () {
            Route::prefix('neuron-payment')->group(function () {
                Route::post('/create', [NeuronPaymentController::class, 'create'])->name('api.neuron-payment.create');
                Route::post('/update', [NeuronPaymentController::class, 'update'])->name('api.neuron-payment.update');
                Route::delete('/delete', [NeuronPaymentController::class, 'delete'])->name('api.neuron-payment.delete');
            });
        });
    });
});
