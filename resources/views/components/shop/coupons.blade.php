@props([
    'order' => []
])
<div {{ $attributes->merge(['class' => '']) }}>
    @foreach($order['coupons'] as $coupon)
        <div class="flex items-center justify-between my-4">
            <x-parsing.coupon-code :code="$coupon['code']"/>
            <div>
                <x-shapes.pill wire:click="removeCoupon('{{ $coupon['code'] }}')"
                        class="mx-2 hover:bg-red-50 hover:border-red-200 hover:text-red-600 cursor-pointer"
                >@include('svg.trash')</x-shapes.pill>
            </div>
        </div>
    @endforeach
</div>
