@if ($value)
    @php
        $value = \Carbon\Carbon::parse($value);
    @endphp
    <span class="block group whitespace-nowrap">
        <span class="">{{ $value->format('d.m.Y') }}</span>
        <span class="opacity-0 group-hover:opacity-100 text-xs text-gray-500">{{ $value->format('H:i:s') }}</span>
    </span>
@endif
