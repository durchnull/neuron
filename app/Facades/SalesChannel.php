<?php

namespace App\Facades;

use App\Contracts\Engine\SalesChannelContract;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string id()
 * @method static \App\Models\Engine\SalesChannel get()
 * @method static SalesChannelContract set(\App\Models\Engine\SalesChannel $salesChannel)
 * @method static SalesChannelContract setByToken(string $token)
 * @method static SalesChannelContract setByCartToken(string $token)
 * @method static bool removeItemsOnPriceIncrease()
 * @method static string getCurrencyCode()
 *
 * @see SalesChannelService
 */
class SalesChannel extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'sales-channel';
    }
}
