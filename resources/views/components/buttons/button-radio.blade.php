@props([
    'active' => false
])
<span {{ $attributes->merge(['class' => 'group inline-flex items-center justify-center w-14 h-8 rounded-full bg-gray-100 border' . ($active ? ' border-blue-100' : ' hover:border-blue-100 active:bg-blue-200 cursor-pointer')]) }}>
    <span class="w-10 h-4 rounded-full {{ $active ? 'bg-blue-500' : 'group-hover:bg-blue-500 transition-colors' }}"></span>
</span>
