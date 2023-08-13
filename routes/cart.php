<?php

use App\Http\Controllers\Engine\CartController;
use Illuminate\Support\Facades\Route;

Route::get('/options', [CartController::class, 'options'])->name('options');
Route::get('/{id}', [CartController::class, 'show'])->name('show'); // @todo [test]
Route::post('/create', [CartController::class, 'create'])->name('create');

Route::post('/amazon-pay-button', [CartController::class, 'amazonPayButton'])->name('amazon-pay-button');

Route::prefix('item')->group(function () {
    Route::post('/add', [CartController::class, 'addItem'])->name('item.add');
    Route::post('/remove', [CartController::class, 'removeItem'])->name('item.remove');
    Route::post('/update', [CartController::class, 'updateItem'])->name('item.update');
});

Route::prefix('update')->group(function () {
    Route::post('/shipping', [CartController::class, 'updateShipping'])->name('update.shipping');
    Route::post('/payment', [CartController::class, 'updatePayment'])->name('update.payment');
    Route::post('/customer', [CartController::class, 'updateCustomer'])->name('update.customer');
});

Route::prefix('coupon')->group(function () {
    Route::post('/redeem', [CartController::class, 'redeemCoupon'])->name('coupon.redeem');
    Route::post('/remove', [CartController::class, 'removeCoupon'])->name('coupon.remove');
});

Route::prefix('transaction')->group(function () {
    Route::post('/create', [CartController::class, 'transactionCreate'])->name('transaction.create');
});

Route::post('/place', [CartController::class, 'place'])->name('place');
