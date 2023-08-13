@php
    $quantityOptions = [];

    foreach (range(1, $item['quantity'] + 10) as $q) {
        $quantityOptions[] = [
            'label' => $q,
            'value' => $q,
            'selected' => $q === (int)$item['quantity'],
        ];
    }
@endphp

<div class="p-4 my-4 rounded group {{ empty($item['reference']) ? 'bg-gray-50' : 'bg-green-50 text-green-600' }}">
    <div class="flex items-start justify-between">
        <x-typography.title class="block mb-4">{{ $item['product']['name'] }}</x-typography.title>
        <div class="flex flex-col items-end justify-center w-24 text-right leading-snug">
            <span>
                @if ($item['total_amount'] - $item['discount_amount'] === 0)
                    <span>Free</span>
                @else
                    <x-parsing.price :amount="$item['total_amount'] - $item['discount_amount']"/>
                @endif
            </span>
            @if ($item['discount_amount'] > 0)
                <span class="text-xs text-green-600">
                    <span>-</span>
                    <x-parsing.price :amount="$item['discount_amount']"/>
                </span>
            @endif
        </div>
    </div>
    <div class="flex items-center justify-between">
        <div class="border rounded overflow-hidden {{ empty($item['reference']) ? '' : 'border-green-200' }}">
            <x-image.product-configuration :configuration="$item['configuration'] ?? []"/>
            <x-image.product-image :src="$item['product']['image_url']"/>
        </div>
        <div class="relative">
            <div class="absolute bg-white px-4 py-3 text-sm border rounded-lg opacity-100 group-hover:opacity-0"
            >{{ $item['quantity'] }}</div>
            <div class="opacity-0 group-hover:opacity-100">
                <x-form.select :options="$quantityOptions"
                               wire:change="updateQuantity('{{ $item['id'] }}', $event.target.value)"
                />
            </div>
        </div>
        <div class="opacity-0 group-hover:opacity-100">
            <x-shapes.pill wire:click="remove('{{ $item['id'] }}')"
                           class="mx-2 bg-white hover:bg-red-50 hover:border-red-200 hover:text-red-600 cursor-pointer"
            >@include('svg.trash')</x-shapes.pill>
        </div>
    </div>
</div>
