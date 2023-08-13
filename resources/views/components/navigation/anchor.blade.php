<a href="{{ $href }}"
        {{ $attributes->merge(['class' => 'inline-flex items-center px-2 py-1 hover:text-blue-600']) }}
>
    @isset($icon)
        @include('svg.' . $icon)
        <span class="ml-2">{{ $slot }}</span>
    @else
        {{ $slot }}
    @endisset
</a>
