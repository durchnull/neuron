<?php

namespace App\Services\Engine;

use App\Contracts\Engine\IntegrationsServiceContract;
use App\Contracts\Engine\SalesChannelContract;
use App\Contracts\Integration\IntegrationServiceContract;
use App\Contracts\Integration\PaymentProvider\PaymentProviderServiceContract;
use App\Enums\Integration\IntegrationTypeEnum;
use App\Integration\Interface\CancelOrder;
use App\Integration\Interface\DistributeOrder;
use App\Integration\Interface\RefundOrder;
use App\Models\Engine\Order;
use App\Models\Integration\Integration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class IntegrationsService implements IntegrationsServiceContract
{
    protected array $integrations;

    public function __construct(
        protected SalesChannelContract $salesChannelService,
        array $integrations
    ) {
        $this->integrations = [];

        foreach ($integrations as $type => $models) {
            if (is_string($type) && in_array($type, array_map(fn(IntegrationTypeEnum $integrationType) => $integrationType->value, IntegrationTypeEnum::cases()))) {
                foreach ($models as $model => $contract) {
                    if (is_string($model) && is_a($model, Integration::class, true)
                        && is_string($contract) && is_a($contract, IntegrationServiceContract::class, true)
                    ) {
                        $this->integrations[$type][$model] = $contract;
                    }
                }
            }
        }
    }

    public function getClasses(array $types = []): array
    {
        $classes = [];

        $types = array_map(
            fn(IntegrationTypeEnum $integrationType) => $integrationType->value,
            $types
        );

        foreach ($this->integrations as $type => $_classes) {
            if (!empty($types) && !in_array($type, $types)) {
                continue;
            }

            $classes = array_merge($classes, array_keys($_classes));
        }

        return $classes;
    }

    public function getModels(array $types = []): Collection {
        $models = new Collection();

        $types = array_map(
            fn(IntegrationTypeEnum $integrationType) => $integrationType->value,
            $types
        );

        foreach ($this->integrations as $type => $classes) {
            if (!empty($types) && !in_array($type, $types)) {
                continue;
            }

            /** @var Integration $class */
            foreach ($classes as $class => $contract) {
                $models = $models->merge(
                    $class::where('sales_channel_id', $this->salesChannelService->id())->get()
                );
            }
        }

        return $models;
    }

    protected function action(Order $order, string $method, string $interface, array $types = []): void
    {
        $types = array_map(
            fn(IntegrationTypeEnum $integrationType) => $integrationType->value,
            $types
        );

        Log::channel('integration')->info($method . ' ' . $order->id . ' ' . json_encode($types));

        foreach ($this->integrations as $type => $classes) {
            if (!empty($types) && !in_array($type, $types)) {
                continue;
            }

            foreach ($classes as $class => $contract) {
                if (is_a($contract, $interface, true)) {
                    /** @var Collection $models */
                    $models = $class::where([
                            'sales_channel_id' => $order->sales_channel_id,
                            'distribute_order' => true,
                            'enabled' => true
                        ])
                        ->get();

                    /** @var Integration $model */
                    foreach ($models as $model) {
                        $service = App::make($contract, [Str::camel(class_basename($class)) => $model]);
                        Log::channel('integration')->info(class_basename($model) . $method . ' with ' . class_basename($service));

                        if (method_exists($service, $method)) {
                            $service->{$method}($order);
                        }
                    }
                }
            }
        }
    }

    public function distributeOrder(Order $order, array $types = []): void
    {
        $this->action($order, 'distributeOrder', DistributeOrder::class, $types);
    }

    public function refundOrder(Order $order, array $types = []): void
    {
        $this->action($order, 'refundOrder', RefundOrder::class, $types);
    }

    public function cancelOrder(Order $order, array $types = []): void
    {
        $this->action($order, 'cancelOrder', CancelOrder::class, $types);
    }

    public function getPaymentProvider(Integration $integration): PaymentProviderServiceContract
    {
        $contract = $this->integrations[IntegrationTypeEnum::PaymentProvider->value][get_class($integration)];

        return App::makeWith($contract, [Str::camel(class_basename($integration)) => $integration]);
    }
}
