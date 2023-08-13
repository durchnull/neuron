@props(['value' => null])
<div {{ $attributes->merge(['class' => 'flex items-center']) }}>
    @php
        $provider = 'integration/' . strtolower(\Illuminate\Support\Str::kebab($value)) . '.jpg';
        $file = asset('/storage/'. $provider);
    @endphp
    <img src="{{ $file }}"
         class="w-8 h-8 rounded border"
    />
</div>
