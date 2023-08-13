<?php

namespace App\Http\Controllers\Engine;

use App\Http\Resources\Engine\StockResource;
use App\Models\Engine\Stock;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockController extends EngineResourceController
{
    public static function getModelClass(): string
    {
        return Stock::class;
    }

    public static function getResourceClass(): string
    {
        return StockResource::class;
    }

    public function create(Request $request): JsonResource
    {
        throw new Exception('Not allowed');
    }

    public function update(Request $request): JsonResource
    {
        throw new Exception('Not allowed');
    }

    /**
     * @throws Exception
     */
    public function delete(Request $request): JsonResponse
    {
        throw new \Exception('Not allowed');
    }
}
