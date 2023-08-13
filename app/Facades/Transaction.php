<?php

namespace App\Facades;

use App\Customer\CustomerProfile;
use App\Services\Engine\TransactionService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Models\Engine\Transaction create(\App\Models\Engine\Order $order, array $resourceData = [], string $resourceId = null)
 * @method static \App\Models\Engine\Transaction update(\App\Models\Engine\Order $order, string $resourceId, array $resourceData = [])
 * @method static \App\Models\Engine\Transaction place(\App\Models\Engine\Order $order, array $resourceData = [])
 * @method static void cancel(\App\Models\Engine\Order $order)
 * @method static void refundOrder(\App\Models\Engine\Order $order)
 * @method static void shipOrder(\App\Models\Engine\Order $order)
 * @method static string getWebhookUrl(\App\Models\Engine\Transaction $transaction)
 * @method static CustomerProfile getCustomerProfile(\App\Models\Engine\Transaction $transaction)
 *
 * @see TransactionService
 */
class Transaction extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'transaction';
    }
}
