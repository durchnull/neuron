@if ($value)
    @php
        $date = \Carbon\Carbon::parse($value)
    @endphp
    <span {{ $attributes->merge(['class' => 'inline-flex items-center text-xs']) }}>
        <span class="font-bold">{{ $date->isToday() ? 'Today' : $date->format('d.m.Y') }}</span>
    </span>
@endif
