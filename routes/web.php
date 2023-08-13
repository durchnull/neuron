<?php

use App\Models\Engine\SalesChannel;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $shops = SalesChannel::all()->map(fn(SalesChannel $salesChannel) => [
         'name' => $salesChannel->name,
         'url' => route('shop', ['id' => $salesChannel->id]),
    ])->toArray();
    return view('pages.home', [
        'shops' => $shops
    ]);
})->name('home');

Route::get('/docs/api', function () {
    return view('pages.docs.api');
})->name('docs.api');

if (App::environment('local')) {
    Route::get('/demo', function () {
        Artisan::call('merchant:demo');

        return redirect()->route('admin.dashboard');
    })->name('demo');

    Route::get('/styling', function () {
        return view('pages.styling');
    })->name('styling');
}
