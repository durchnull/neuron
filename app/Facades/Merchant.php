<?php

namespace App\Facades;

use App\Contracts\Engine\MerchantServiceContract;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Models\Engine\Merchant new()
 * @method static \App\Models\Engine\Merchant get()
 * @method static MerchantServiceContract set(\App\Models\Engine\Merchant $merchant)
 * @method static MerchantServiceContract setByToken(string $token)
 * @method static string id()
 *
 * @see MerchantService
 */
class Merchant extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'merchant';
    }
}
