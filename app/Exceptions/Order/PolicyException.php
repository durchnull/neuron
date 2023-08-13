<?php

namespace App\Exceptions\Order;

use App\Actions\Actionable;
use App\Enums\Order\PolicyReasonEnum;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PolicyException extends Exception
{
    protected Actionable $actionable;

    /**
     * @throws Exception
     */
    public function __construct(Actionable $actionable)
    {
        $this->actionable = $actionable;

        if (!$actionable->denied()) {
            throw new Exception('Can not convert undenied Action to PolicyException');
        }

        parent::__construct($this->summarize(), 429);
    }

    /**
     * @return string
     */
    public function summarize(): string
    {
        $policies = $this->getPolicies();

        /** @var PolicyReasonEnum $firstPolicy */
        $firstPolicy = reset($policies);

        return __('policies.' . $firstPolicy->value . '.description') . (count($policies) > 1 ? ' (' . (count($policies) - 1) . ' more)' : '');
    }

    /**
     * @return array
     */
    public function getPolicies(): array
    {
        return $this->actionable->policies();
    }

    /**
     * @return array
     */
    protected function renderData(): array
    {
        return [
            'message' => $this->summarize(),
            'code' => $this->getPolicies()[0]->value
        ];
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json($this->renderData(), 429);
    }
}
