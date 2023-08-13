<?php

namespace App\Services\Integration\Inventory;

use App\Contracts\Integration\Inventory\NeuronInventoryServiceContract;
use App\Models\Engine\Order;
use App\Models\Integration\OrderIntegration;
use App\Models\Integration\Inventory\NeuronInventory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NeuronInventoryService implements NeuronInventoryServiceContract
{
    public function __construct(
        protected NeuronInventory $neuronInventory
    ) {
    }

    public static function getClientVersion(): string
    {
        return config('app.version');
    }

    public function test(): bool
    {
        return true;
    }

    public function distributeOrder(Order $order): void
    {
        $orderIntegration = OrderIntegration::create([
            'order_id' => $order->id,
            'integration_id' => $this->neuronInventory->id,
            'integration_type' => get_class($this->neuronInventory),
            'resource_id' => Str::uuid()->toString(),
            'status' => 'distributed',
        ]);

        Log::channel('integration')->info('Created order integration ' . $orderIntegration->id);
    }
}
