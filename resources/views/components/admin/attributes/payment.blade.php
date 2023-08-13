@if ($value instanceof \App\Models\Engine\Payment)
    <span class="whitespace-nowrap">
        <span class="block mb-1">{{ $value->name }}</span>
        <span class="block text-xs text-gray-500">{{ $value->provider }}</span>
    </span>
@else
    <span class="opacity-25">-</span>
@endif
