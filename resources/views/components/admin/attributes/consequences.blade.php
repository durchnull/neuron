@if ($value instanceof \App\Consequence\ConsequenceCollection)
    <span class="whitespace-nowrap">
        <span class="block text-xs text-gray-500">{{ json_encode($value->toArray()) }}</span>
    </span>
@else
    <span class="opacity-25">-</span>
@endif
