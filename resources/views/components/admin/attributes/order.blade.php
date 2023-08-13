@if ($value instanceof \App\Models\Engine\Order)
    <span class="whitespace-nowrap">
        <span class="block mb-1">{{ $value->order_number }}</span>
        <span class="block text-xs text-gray-500">
            <x-admin.attributes.money :value="$value->amount"/>
        </span>
        <span class="block text-xs text-gray-500">{{ $value->payment->name }}</span>
    </span>
@else
    <span class="opacity-25">-</span>
@endif
