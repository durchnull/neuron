@if ($value instanceof \App\Models\Engine\SalesChannel)
    <span class="whitespace-nowrap">
        <span class="block mb-1">{{ $value->name }}</span>
        <span class="block text-xs text-gray-500">{{ $value->id }}</span>
    </span>
@else
    <span class="opacity-25">-</span>
@endif
