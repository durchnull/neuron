<?php

namespace App\Providers;

use App\Http\Middleware\AuthenticateCart;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/admin';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            // @todo [api]
            return Limit::perMinute(1000)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('cart', function (Request $request) {
            // @todo [api]
            return Limit::perMinute(100)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('web')
                ->prefix('admin')
                ->group(base_path('routes/admin.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware([
                    'cart',
                    AuthenticateCart::class
                ])
                ->prefix('cart')
                ->name('cart.')
                ->group(base_path('routes/cart.php'));

            Route::middleware('web')
                ->prefix('shop')
                ->group(base_path('routes/shop.php'));

            Route::prefix('integration')
                ->group(base_path('routes/integration.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

        });
    }
}
