<?php

namespace App\Facades;

use App\Contracts\Integration\PaymentProvider\PaymentProviderServiceContract;
use App\Models\Engine\Order;
use App\Models\Engine\SalesChannel;
use App\Models\Integration\Integration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array getClasses(array $types = []);
 * @method static Collection getModels(array $types = []);
 * @method static void distributeOrder(Order $order, array $types = []);
 * @method static void cancelOrder(Order $order, array $types = []);
 * @method static void refundOrder(Order $order, array $types = []);
 * @method static PaymentProviderServiceContract getPaymentProvider(Integration $integration);
 *
 * @see IntegrationsService
 */
class Integrations extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'integrations';
    }
}
