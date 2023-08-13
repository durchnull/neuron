<x-admin.layout>
    <x-slot name="headline">{{ $name }}</x-slot>
    <x-slot name="actions">
        <x-form.toggle model="enabled" :value="$enabled"/>
    </x-slot>
    <x-admin.anchor-back href="{{ route('admin.products') }}"/>
    <x-form.form>
        <x-grid-3>
            <x-blocks.card class="flex">
                <div class="mr-4">
                    <x-label class="block mb-4">Type</x-label>
                    <div class="flex items-center">
                        <x-typography.big-text class="mr-2">{{ ucfirst($type) }}</x-typography.big-text>
                        <x-admin.attributes.product_type :value="\App\Enums\Product\ProductTypeEnum::from($type)"/>
                    </div>
                </div>
                <div>
                    <x-label class="block mb-4">Version</x-label>
                    <x-typography.big-text class="mt-4">{{ $version }}</x-typography.big-text>
                </div>
            </x-blocks.card>
            <x-blocks.card class="flex col-span-2">
                <div class="mr-4">
                    <x-label class="block mb-4">Inventory</x-label>
                    <x-typography.big-text>
                        <x-admin.attributes.inventory :value="$inventoryType"/>
                    </x-typography.big-text>
                </div>
                <div class="mr-4">
                    <x-label class="block mb-4">Stock</x-label>
                    <x-typography.big-text>{{ $stock }}</x-typography.big-text>
                </div>
                <div class="mr-4">
                    <x-label class="block mb-4">Id</x-label>
                    <x-typography.big-text>{{ $inventoryId }}</x-typography.big-text>
                </div>
            </x-blocks.card>
        </x-grid-3>
        <x-grid-2>
            <x-form.text model="name"
                         label="Name"
            />
            <x-form.text model="sku"
                         label="Sku"
            />
            <x-form.price model="netPrice"
                          label="Price (net) in EUR"
            />
            <x-form.price model="grossPrice"
                          label="Price (gross) in EUR"
            />
        </x-grid-2>
    </x-form.form>

    <x-grid-3>
        @foreach($productPriceIds as $productPriceId)
            <x-blocks.card>
                <livewire:admin.engine.product-price :id="$productPriceId"/>
            </x-blocks.card>
        @endforeach
    </x-grid-3>

    @if ($type === \App\Enums\Product\ProductTypeEnum::Bundle->value)
        <x-typography.headline class="my-16">Configuration</x-typography.headline>
        <div class="grid grid-cols-{{ count($configuration) }} gap-4">
            @foreach($configuration as $index => $group)
                <x-blocks.card>
                    @foreach($group as $productId)
                        @php
                            $product = \App\Models\Engine\Product::find($productId);
                        @endphp
                        <div class="flex items-center my-4">
                            <div class="border rounded mr-4">
                                <x-image.product-image :src="$product->image_url"/>
                            </div>
                            <x-typography.title class="flex-1">{{ $product->name }}</x-typography.title>
                            <x-typography.small>{{ \App\Facades\Stock::get($product->id) }}</x-typography.small>
                        </div>
                    @endforeach
                </x-blocks.card>
            @endforeach
        </div>
    @endif
</x-admin.layout>
