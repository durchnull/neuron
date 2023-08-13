@if ($conditionCollection instanceof \App\Condition\ConditionCollection)
    <span>{{ json_encode($conditionCollection->toArray()) }}</span>
@endif
