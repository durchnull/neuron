<span class="inline-flex">
    @foreach($configuration as $productId)
        @php
            $image_url = \App\Models\Engine\Product::where('id', $productId)->value('image_url');
        @endphp
        <x-image.product-image :src="$image_url"/>
    @endforeach
</span>
