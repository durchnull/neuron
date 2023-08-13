@if ($value)
    @php
        $dateTime = \Carbon\Carbon::parse($value)
    @endphp
    <span {{ $attributes->merge(['class' => 'inline-flex items-center text-xs']) }}>
        <span class="font-bold mr-2">{{ $dateTime->format('d.m.Y') }}</span>
        <span class="">{{ $dateTime->format('H:i:s') }}</span>
    </span>
@endif
