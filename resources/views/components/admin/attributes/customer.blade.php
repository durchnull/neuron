@if ($value instanceof \App\Models\Engine\Customer)
    <span class="whitespace-nowrap">
        <span class="block mb-1">{{ $value->full_name }}</span>
        <span class="block text-xs text-gray-500">{{ $value->email }}</span>
    </span>
@else
    <span class="opacity-25">-</span>
@endif
