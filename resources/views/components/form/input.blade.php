<x-form.group :label="$label ?? null"
              :model="$model"
>
    <input type="{{ $type }}"
           id="input-{{ $model }}"
           name="{{ $model }}"
           class="px-4 py-2 border-2 rounded-lg @error($model) border-red-200 @enderror"
           placeholder="{{ $placeholder ?? $label ?? null }}"
           wire:model="{{ $model }}"
    >
</x-form.group>
