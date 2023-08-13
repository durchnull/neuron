@props([
    'href' => '',
    'active' => false,
    'icon' => null,
])
<a href="{{ $href }}"
        {{ $attributes->merge(['class' => 'flex items-center justify-between px-4 py-4 h-14 border-l-4'
            . ($active ? ' font-bold border-slate-600' : ' hover:bg-slate-50 border-transparent'
        )]) }}
>
    @if(isset($icon))
        <span class="w-full mr-4">{{ $slot }}</span>
        @include('svg.' . $icon)
    @else
        {{ $slot }}
    @endif
</a>
