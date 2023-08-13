@props([
    'order' => []
])
<div {{ $attributes->merge(['class' => '']) }}>
    @foreach($order['cart_rules'] as $cartRule)
        <x-shapes.pill class="border-green-500">
            <span class="text-green-500">@include('svg.gift')</span>
            <span class="ml-2 mr-1">{{ $cartRule['name'] }}</span>
        </x-shapes.pill>
    @endforeach
</div>
