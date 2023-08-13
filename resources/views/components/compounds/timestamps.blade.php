@props([
    'created_at' => null,
    'updated_at' => null,
])
<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center justify-between -mx-2 my-4 text-gray-500']) }}>
    <div class="flex-1 bg-gray-50 px-4 py-4 rounded m-2">
        <div class="text-xs">Created</div>
        <x-parsing.date-time :value="$created_at"/>
    </div>
    <div class="flex-1 bg-gray-50 px-4 py-4 rounded m-2">
        <div class="text-xs">Updated</div>
        <x-parsing.date-time :value="$updated_at"/>
    </div>
</div>
