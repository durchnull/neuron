@props([
    'productId' => $productId,
    'quantity' => $quantity,
])
<div {{ $attributes->merge(['class' => 'flex items-center select-none']) }}>
    <x-shapes.pill wire:click="decrementQuantity('{{ $productId }}')"
        class="mx-1 {{ $quantity === 1 ? 'opacity-25 bg-gray-100' : 'cursor-pointer bg-white hover:border-gray-400 active:bg-gray-100' }}"
    >@include('svg.arrow-small-left')</x-shapes.pill>
    <span class="font-bold w-10 text-center">{{ $quantity }}</span>
    <x-shapes.pill wire:click="incrementQuantity('{{ $productId }}')"
            class="mx-1 cursor-pointer bg-white hover:border-gray-400 active:bg-gray-100"
    >@include('svg.arrow-small-right')</x-shapes.pill>

</div>
