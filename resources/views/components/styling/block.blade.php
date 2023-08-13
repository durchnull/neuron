<div {{ $attributes->merge(['class' => 'my-4 p-4 rounded bg-white']) }}>
    @isset($title)
        <x-typography.small class="block my-4">{{ $title }}</x-typography.small>
        <x-shapes.separator class="border-dashed"/>
    @endisset
    <div class="my-8">
        {{ $slot }}
    </div>
</div>
