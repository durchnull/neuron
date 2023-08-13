@php
    $provider = 'payment-provider/' . strtolower(\Illuminate\Support\Str::slug($value->value)) . '.jpg';
    $file = asset('/storage/'. $provider);
@endphp
<img src="{{ $file }}"
     class="w-8 h-8 rounded border"
/>
