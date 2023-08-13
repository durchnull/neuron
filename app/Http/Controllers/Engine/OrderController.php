<?php

namespace App\Http\Controllers\Engine;

use App\Http\Resources\Engine\OrderResource;
use App\Models\Engine\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderController extends EngineResourceController
{

    public static function getModelClass(): string
    {
        return Order::class;
    }

    public static function getResourceClass(): string
    {
        return OrderResource::class;
    }

    public function create(Request $request): JsonResource
    {
        throw new \Exception('Use ' . CartController::class);
    }

    public function update(Request $request): JsonResource
    {
        throw new \Exception('Use ' . CartController::class);
    }

    public function refund(Request $request): JsonResource
    {
        // @todo
    }

    public function cancel(Request $request): JsonResource
    {
        // @todo
    }
}
