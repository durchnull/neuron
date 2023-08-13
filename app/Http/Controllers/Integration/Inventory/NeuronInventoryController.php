<?php

namespace App\Http\Controllers\Integration\Inventory;

use App\Actions\Integration\Inventory\NeuronInventory\NeuronInventoryCreateAction;
use App\Enums\TriggerEnum;
use App\Facades\SalesChannel;
use App\Http\Controllers\Controller;
use App\Http\Resources\Integration\Inventory\NeuronInventoryResource;
use App\Models\Integration\Inventory\NeuronInventory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;

class NeuronInventoryController extends Controller
{
    /**
     * @param  Request  $request
     * @return JsonResource
     * @throws ValidationException
     * @throws Exception
     */
    public function create(Request $request): JsonResource
    {
        $neuronInventory = $this->action(
            new NeuronInventoryCreateAction(
                new NeuronInventory(),
                array_merge($request->all(), [
                    'merchant_id' => SalesChannel::get()->merchant_id
                ]),
                TriggerEnum::Api
            )
        );

        return new NeuronInventoryResource($neuronInventory);
    }
}
