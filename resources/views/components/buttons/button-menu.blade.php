@props([
    'event' => ''
])
<x-buttons.button {{ $attributes }}
    @click="$dispatch('{{ $event }}')"
>
    @include('svg.bars-3')
</x-buttons.button>
