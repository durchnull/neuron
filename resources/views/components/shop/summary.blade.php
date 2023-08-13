@props([
    'order' => []
])
<div {{ $attributes->merge(['class' => '']) }}>
    <div class="p-4 rounded bg-gray-50">
        @isset($order['customer'])
            <div class="flex items-center justify-between my-4">
                <div>Customer</div>
                <div>{{ $order['customer']['email'] }}</div>
            </div>
        @endisset
        <div class="flex items-center justify-between my-4">
            <div>Payment</div>
            <div>{{ $order['payment']['name'] }}</div>
        </div>
        <div class="flex items-center justify-between my-4">
            <div>Delivery</div>
            <div>{{ $order['shipping']['name'] }} - {{ $order['shipping']['country_code'] }}</div>
        </div>
    </div>
    <div class="px-4">
        @if (!empty($order['items']))
            <div class="flex items-start justify-between my-4">
                <div>Cart</div>
                <div class="flex flex-col items-end">
                    <span>
                        @if ($order['items_amount'] - $order['items_discount_amount'] === 0)
                            <span>Free</span>
                        @else
                            <x-parsing.price :amount="$order['items_amount'] - $order['items_discount_amount']"/>
                        @endif
                    </span>
                    @if ($order['items_discount_amount'] > 0)
                        <span class="text-xs text-green-600">
                        <span>-</span>
                        <x-parsing.price :amount="$order['items_discount_amount']"/>
                    </span>
                    @endif
                </div>
            </div>
        @endif
        <div class="flex items-start justify-between my-4">
            <div>Shipping</div>
            <div class="flex flex-col items-end">
                <span>
                    @if ($order['shipping_amount'] - $order['shipping_discount_amount'] === 0)
                        <span>Free</span>
                    @else
                        <x-parsing.price :amount="$order['shipping_amount']"/>
                    @endif
                </span>
                @if ($order['shipping_discount_amount'] > 0)
                    <span class="text-green-600">
                    <span>-</span>
                    <x-parsing.price :amount="$order['shipping_discount_amount']"/>
                </span>
                @endif
            </div>
        </div>
        <div class="flex items-center justify-between my-4 font-bold">
            <div>Totals</div>
            <x-parsing.price :amount="$order['amount']"/>
        </div>
    </div>
</div>
