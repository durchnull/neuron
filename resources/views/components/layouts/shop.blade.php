<x-layouts.elements.html>
    <x-slot name="head">
        <x-layouts.elements.head/>
    </x-slot>
    <div class="flex items-center gap-4 max-w-6xl mx-auto p-4 text-xs font-bold">
        <a href="{{ route('admin.home') }}"
           class="hover:text-blue-700"
        >Admin</a>
        <a href="{{ route('shop', ['id' => \App\Facades\SalesChannel::id()]) }}"
           class="hover:text-blue-700"
        >Shop</a>
        <a href="{{ route('shop.checkout', ['id' => \App\Facades\SalesChannel::id()]) }}"
           class="hover:text-blue-700"
        >Checkout</a>
        <a href="{{ route('shop.order', ['id' => \App\Facades\SalesChannel::id(), 'orderId' => 'orderId', 'orderNumber' => 'orderNumber']) }}"
           class="hover:text-blue-700"
        >Order</a>
    </div>
    {{ $slot }}
</x-layouts.elements.html>
