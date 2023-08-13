<?php

use App\Http\Controllers\Integration\Inventory\BillbeeController;
use App\Http\Controllers\Integration\PaymentProvider\MollieController;
use App\Http\Controllers\Integration\PaymentProvider\NeuronPaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('inventory')->group(function () {
    Route::prefix('billbee')->group(function () {
        Route::any('/{salesChannelId}/{billbeeId}', [BillbeeController::class, 'entry'])->name('integration.billbee.entry');
    });
});

Route::prefix('payment-provider')->group(function () {
    Route::prefix('neuron-payment')->group(function () {
        Route::post('/{orderId}/{webhookId}', [NeuronPaymentController::class, 'transaction'])->name('integration.neuron-payment.transaction');
    });
    Route::prefix('mollie')->group(function () {
        Route::post('/{orderId}/{webhookId}', [MollieController::class, 'transaction'])->name('integration.mollie.transaction');
    });
});
