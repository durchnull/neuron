<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Engine\ActionRule;
use App\Livewire\Admin\Engine\CartRule;
use App\Livewire\Admin\Engine\Condition;
use App\Livewire\Admin\Engine\Order;
use App\Livewire\Admin\Engine\Product;
use App\Livewire\Admin\Engine\Resource;
use App\Livewire\Admin\Engine\Rule;
use App\Livewire\Admin\Integration\AmazonPay;
use App\Livewire\Admin\Integration\Billbee;
use App\Livewire\Admin\Integration\Mailgun;
use App\Livewire\Admin\Integration\Mollie;
use App\Livewire\Admin\Integration\NeuronInventory;
use App\Livewire\Admin\Integration\Paypal;
use App\Livewire\Admin\Integration\Weclapp;
use App\Livewire\Admin\Integrations;
use App\Livewire\Admin\List\ActionRules;
use App\Livewire\Admin\List\CartRules;
use App\Livewire\Admin\List\Carts;
use App\Livewire\Admin\List\Conditions;
use App\Livewire\Admin\List\Coupons;
use App\Livewire\Admin\List\Customers;
use App\Livewire\Admin\List\Orders;
use App\Livewire\Admin\List\Payments;
use App\Livewire\Admin\List\Products;
use App\Livewire\Admin\List\Rules;
use App\Livewire\Admin\List\SalesChannels;
use App\Livewire\Admin\List\Shippings;
use App\Livewire\Admin\User;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])
    ->group(function () {
        Route::get('/', Dashboard::class)->name('admin.home');
        Route::get('/user', User::class)->name('admin.user');
        Route::get('/resource/{class}/{id}', Resource::class)->name('admin.engine.resource');
        Route::get('/dashboard', Dashboard::class)->name('admin.dashboard');
        Route::prefix('carts')->group(function () {
            Route::get('/', Carts::class)->name('admin.carts');
            Route::get('/{id}', Order::class)->name('admin.engine.order');
        });
        Route::get('/orders', Orders::class)->name('admin.orders');
        Route::prefix('products')->group(function () {
            Route::get('/', Products::class)->name('admin.products');
            Route::get('/{id}', Product::class)->name('admin.engine.product');
        });
        Route::get('/customers', Customers::class)->name('admin.customers');
        Route::get('/coupons', Coupons::class)->name('admin.coupons');
        Route::prefix('conditions')->group(function () {
            Route::get('/', Conditions::class)->name('admin.conditions');
            Route::get('/{id}', Condition::class)->name('admin.engine.condition');
        });
        Route::prefix('rules')->group(function () {
            Route::get('/', Rules::class)->name('admin.rules');
            Route::get('/{id}', Rule::class)->name('admin.engine.rule');
        });
        Route::prefix('cart-rules')->group(function () {
            Route::get('/', CartRules::class)->name('admin.cart-rules');
            Route::get('/{id}', CartRule::class)->name('admin.engine.cart-rule');
        });
        Route::prefix('action-rules')->group(function () {
            Route::get('/', ActionRules::class)->name('admin.action-rules');
            Route::get('/{id}', ActionRule::class)->name('admin.engine.action-rule');
        });
        Route::get('/shippings', Shippings::class)->name('admin.shippings');
        Route::get('/payments', Payments::class)->name('admin.payments');
        Route::get('/sales-channels', SalesChannels::class)->name('admin.sales-channels');
        Route::prefix('integration')->group(function () {
            Route::get('/', Integrations::class)->name('admin.integration');
            Route::get('/billbee/{id}', Billbee::class)->name('admin.integration.inventory.billbee');
            Route::get('/neuron-inventory/{id}', NeuronInventory::class)->name('admin.integration.inventory.neuron-inventory');
            Route::get('/weclapp/{id}', Weclapp::class)->name('admin.integration.inventory.weclapp');
            Route::get('/mailgun/{id}', Mailgun::class)->name('admin.integration.mail.mailgun');
            Route::get('/mollie/{id}', Mollie::class)->name('admin.integration.payment-provider.mollie');
            Route::get('/paypal/{id}', Paypal::class)->name('admin.integration.payment-provider.paypal');
            Route::get('/amazon-pay/{id}', AmazonPay::class)->name('admin.integration.payment-provider.amazon-pay');
        });
    });

