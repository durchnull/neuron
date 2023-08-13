@if ($value)
    @php
        $carbon = \Carbon\Carbon::parse($value)
    @endphp
    <span {{ $attributes->merge(['class' => 'inline-flex items-center text-xs']) }}
    >{{ $carbon->diffForHumans() }}</span>
@endif
