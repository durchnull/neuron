<?php

namespace App\Http\Controllers;

use App\Actions\Actionable;
use App\Enums\TriggerEnum;
use App\Exceptions\Order\PolicyException;
use App\Facades\SalesChannel;
use App\Http\Requests\IndexRequest;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class ResourceController extends Controller
{
    abstract public static function getActionNamespace(): string;

    abstract public static function getModelClass(): string;

    abstract public static function getResourceClass(): string;


    /**
     * @param  Actionable  $action
     * @return mixed
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    protected function action(Actionable $action): mixed
    {
        $action->trigger();

        return $action->target();
    }

    /**
     * @param  Request  $request
     * @return JsonResource
     * @throws ValidationException
     */
    public function show(Request $request): JsonResource
    {
        $validator = Validator::make([
            'id' => $request->post('id')
        ], [
            'id' => 'required|uuid|exists:' . (new (static::getModelClass())())->getTable()
        ]);

        if ($validator->fails()) {
            throw new ModelNotFoundException();
        }

        $model = static::getModelClass()::where('sales_channel_id', SalesChannel::get()->id)
            ->find($validator->validated()['id']);

        return new (static::getResourceClass())($model);
    }

    /**
     * @param  IndexRequest  $request
     * @return ResourceCollection
     */
    public function index(IndexRequest $request): ResourceCollection
    {
        $ids = $request->validated()['id'] ?? [];

        $query = static::getModelClass()::where('sales_channel_id', SalesChannel::get()->id);

        if (!empty($ids)) {
            $query = $query->whereIn('id', $request->validated()['id']);
        }

        $models = $query->get();

        return static::getResourceClass()::collection($models);
    }

    /**
     * @param  Request  $request
     * @return JsonResource
     * @throws ValidationException
     * @throws Exception
     */
    public function create(Request $request): JsonResource
    {
        $className = class_basename(static::getModelClass());
        $createActionClass = static::getActionNamespace() . "\\$className\\{$className}CreateAction";

        $model = $this->action(
            new $createActionClass(
                new (static::getModelClass())(),
                $request->all(),
                TriggerEnum::Api
            )
        );

        return new (static::getResourceClass())($model);
    }

    /**
     * @param  Request  $request
     * @return JsonResource
     * @throws ValidationException
     * @throws Exception
     */
    public function update(Request $request): JsonResource
    {
        // @todo validation
        $id = $request->post('id');

        $model = static::getModelClass()::where('sales_channel_id', SalesChannel::get()->id)
            ->findOrFail($id);

        $className = class_basename(static::getModelClass());
        $updateActionClass = static::getActionNamespace() . "\\$className\\{$className}UpdateAction";

        $model = $this->action(
            new $updateActionClass(
                $model,
                $request->all(),
                TriggerEnum::Api
            )
        );

        return new (static::getResourceClass())($model);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     * @throws PolicyException
     * @throws ValidationException
     */
    public function delete(Request $request): JsonResponse
    {
        // @todo validation
        $id = $request->post('id');

        $model = static::getModelClass()::where('sales_channel_id', SalesChannel::get()->id)
            ->findOrFail($id);

        $className = class_basename(static::getModelClass());
        $deleteActionClass = static::getActionNamespace() . "\\$className\\{$className}DeleteAction";

        // @todo action response?
        $this->action(
            new $deleteActionClass(
                $model,
                $request->all(),
                TriggerEnum::Api
            )
        );

        // @todo delete response
        return response()->json([
            'data' => 'deleted'
        ], 200);
    }
}
