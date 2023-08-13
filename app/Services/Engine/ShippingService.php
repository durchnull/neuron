<?php

namespace App\Services\Engine;

use App\Actions\Engine\Order\OrderUpdateShippingAction;
use App\Contracts\Engine\SalesChannelContract;
use App\Contracts\Engine\ShippingServiceContract;
use App\Customer\CustomerProfile;
use App\Enums\TriggerEnum;
use App\Exceptions\Order\PolicyException;
use App\Models\Engine\Order;
use App\Models\Engine\Shipping;
use Exception;
use Illuminate\Validation\ValidationException;

class ShippingService implements ShippingServiceContract
{
    public function __construct(protected SalesChannelContract $salesChannelService)
    {
    }

    public function getByGuessing(array $attributes): ?Shipping
    {
        if (isset($attributes['country_code'])) {
            if ($shipping = $this->getByCountryCode($attributes['country_code'])) {
                return $shipping;
            }
        }

        return Shipping::where('sales_channel_id', $this->salesChannelService->id())->first();
    }

    public function getCountryCodes(): array
    {
        return Shipping::where('sales_channel_id', $this->salesChannelService->id())
            ->where('enabled', true)
            ->pluck('country_code')
            ->unique()
            ->toArray();
    }

    public function getByCountryCode(string $countryCode): ?Shipping
    {
        return Shipping::where('sales_channel_id', $this->salesChannelService->id())
            ->where('country_code', $countryCode)
            ->orderBy('net_price', 'asc')
            ->first();
    }

    /**
     * @param  Order  $order
     * @param  CustomerProfile  $customerProfile
     * @return Order
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function updateOrder(Order $order, CustomerProfile $customerProfile): Order
    {
        if ($order->shipping->country_code !== $customerProfile->shippingCountryCode) {
            /** @var Shipping $shipping */
            $shipping = $this->getByCountryCode($customerProfile->shippingCountryCode);

            // @todo [test]
            if (!$shipping) {
                throw new \Exception('No shipping to country ' . $customerProfile->shippingCountryCode);
            }

            $action = new OrderUpdateShippingAction(
                $order,
                [
                    'order_id' => $order->id,
                    'shipping_id' => $shipping->id,
                ],
                TriggerEnum::Api
            );

            $action->trigger();

            $order = $action->target();
        }

        return $order;
    }
}
