@if ($value instanceof \App\Models\Engine\Stock)
    <span class="whitespace-nowrap">
        <span class="block mb-1">{{ $value->value }}</span>
        <span class="block text-xs text-gray-500">{{ $value->queue }}</span>
    </span>
@else
    <span class="opacity-25">-</span>
@endif
