@props([
    'href' => $href,
    'icon' => null,
])
<a href="{{ $href }}"
    {{ $attributes->merge(['class' => 'inline-flex items-center p-4 border rounded-lg hover:text-blue-600 hover:border-blue-600']) }}
>
    @isset($icon)
        @include('svg.' . $icon)
        <span class="ml-2">{{ $slot }}</span>
    @else
        {{ $slot }}
    @endisset
</a>
