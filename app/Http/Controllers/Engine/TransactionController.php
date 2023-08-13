<?php

namespace App\Http\Controllers\Engine;

use App\Http\Resources\Engine\TransactionResource;
use App\Models\Engine\Transaction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionController extends EngineResourceController
{
    public static function getModelClass(): string
    {
        return Transaction::class;
    }

    public static function getResourceClass(): string
    {
        return TransactionResource::class;
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
        throw new Exception('Not allowed');
    }
}
