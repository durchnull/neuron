<?php

namespace App\Actions\Engine\Condition;

use App\Enums\Order\PolicyReasonEnum;
use Illuminate\Support\Facades\DB;

class ConditionDeleteAction extends ConditionAction
{
    public static function rules(): array
    {
        return [];
    }

    protected function gate(array $attributes): void
    {
        $exists = DB::table('action_rules')
            ->select('condition_id')
            ->where('condition_id', $this->target->id)
            ->union(
                DB::table('rules')
                    ->select('condition_id')
                    ->where('condition_id', $this->target->id)
            )
            ->exists();

        if ($exists) {
            $this->addPolicy(PolicyReasonEnum::ModelIsReferenced);
        }
    }

    protected function apply(): void
    {
        $this->target->delete();
    }
}
