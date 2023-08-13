<?php

use App\Models\Engine\Product;
use App\Models\Engine\SalesChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/{id}', function (Request $request) {
    /** @var SalesChannel $salesChannel */
    $salesChannel = SalesChannel::findOrFail($request->id);
    \App\Facades\SalesChannel::set($salesChannel);

    return view('pages.shop.shop', [
        'apiUrl' => config('app.url'),
        'apiToken' => $salesChannel->cart_token,
        'shopUrl' => route('shop', ['id' => $salesChannel->id]),
        'products' => Product::where('sales_channel_id', \App\Facades\SalesChannel::id())
            ->get()
            ->map(fn(Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->getPrice(),
                'image_url' => $product->image_url,
            ])->toArray()
    ]);
})->name('shop');

Route::get('/{id}/checkout', function (Request $request) {
    /** @var SalesChannel $salesChannel */
    $salesChannel = SalesChannel::findOrFail($request->id);
    \App\Facades\SalesChannel::set($salesChannel);

    return view('pages.shop.checkout', [
        'apiUrl' => config('app.url'),
        'apiToken' => $salesChannel->cart_token,
        'shopUrl' => route('shop', ['id' => $salesChannel->id]),
    ]);
})->name('shop.checkout');

Route::get('/{id}/order/{orderId}/{orderNumber}', function (Request $request) {
    /** @var SalesChannel $salesChannel */
    $salesChannel = SalesChannel::findOrFail($request->id);
    \App\Facades\SalesChannel::set($salesChannel);

    return view('pages.shop.order', [
        'apiUrl' => config('app.url'),
        'apiToken' => $salesChannel->cart_token,
        'shopUrl' => route('shop', ['id' => $salesChannel->id]),
    ]);
})->name('shop.order');
