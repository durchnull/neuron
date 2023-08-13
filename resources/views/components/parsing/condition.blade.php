@if ($condition instanceof \App\Models\Engine\Condition)
    <span class="whitespace-nowrap">
        <span class="block mb-1">{{ $condition->name }}</span>
        <span class="block text-xs text-gray-500">{{ json_encode($condition->collection->toArray()) }}</span>
    </span>
@else
    <span class="opacity-25">-</span>
@endif
