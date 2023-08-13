@props(['value' => $value])
<div {{ $attributes->merge(['class' => 'group flex whitespace-nowrap italic']) }}>
    @foreach(str_split($value) as $character)
        <span class="relative flex items-center relative">
            <span class="opacity-0 group-hover:opacity-100 transition delay-{{ $loop->index * 50 }}">{{ $character }}</span>
            <span class="absolute inset-0 group-hover:opacity-0 transition delay-{{ $loop->index * 50 }} select-none pointer-events-none">*</span>
        </span>
    @endforeach
</div>
