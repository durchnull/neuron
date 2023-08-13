@section('head')
    <script src="{{ asset('neuron.js') }}"></script>
@endsection
<x-layouts.shop>
    <div id="cart"></div>
    <script>
        Neuron('{{ $apiUrl }}', '{{ $apiToken }}', '{{ $shopUrl }}')
    </script>
    <x-blocks.section class="flex justify-end">
        <button type="button"
                onclick="Neuron.open()"
                class="flex items-center px-6 py-2 bg-white rounded-full border hover:border-blue-300 hover:bg-blue-50 cursor-pointer"
        >
            @include('svg.shopping-cart')
            <x-shapes.circle
                id="cart-items-quantity"
                class="ml-4 text-xs font-bold"
            >
            </x-shapes.circle>
            <script>
                document.getElementById('cart-items-quantity').innerHTML = Neuron.quantity()
            </script>
        </button>
    </x-blocks.section>
    <x-blocks.section>
        <x-grid-3>
            @foreach($products as $product)
                <x-blocks.card>
                    <div class="flex items-center justi">
                        <img src="{{ $product['image_url'] }}"
                             class="w-12 h-12"
                        >
                        <x-typography.title class="mx-2">{{ $product['name'] }}</x-typography.title>
                    </div>
                    <div class="flex items-center mt-4">
                        <x-parsing.price
                            :amount="$product['price']"
                            class="flex-1 mx-2"
                        />
                        <x-buttons.button
                            onclick="
                                Neuron.add('{{ $product['id'] }}', 1, null, () => {
                                    document.getElementById('cart-items-quantity').innerHTML = Neuron.quantity()
                                });
                                Neuron.open()
                            "
                            color="green"
                            class="flex px-8 justify-center whitespace-nowrap"
                        >@include('svg.shopping-cart')
                        </x-buttons.button>
                    </div>
                </x-blocks.card>
            @endforeach
        </x-grid-3>
    </x-blocks.section>
</x-layouts.shop>
