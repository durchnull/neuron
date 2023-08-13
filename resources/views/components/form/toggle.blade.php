@props([
    'model' => $model,
    'value' => $value,
])
<label {{ $attributes->merge(['class' => 'group inline-block cursor-pointer select-none']) }}>
    <input type="checkbox"
           wire:model.live="{{ $model }}"
           class="absolute opacity-0"
       />
    <span class="flex items-center w-16 h-8 p-1 border rounded-full shadow-inner bg-gradient-to-r {{ $value ? 'justify-end from-green-200 to-green-100 border-green-300' : 'from-gray-100 to-gray-200 border-gray-300' }}">
        <span class="flex items-center w-6 h-6 shadow rounded-full transform {{ $value ? 'group-hover:bg-green-700 bg-green-600' : 'group-hover:bg-gray-600 bg-gray-400' }}">
        </span>
    </span>
</label>
