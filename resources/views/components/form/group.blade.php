<div class="flex flex-col my-4">
    @isset($label)
        <div class="flex items-center justify-between">
            <label for="input-{{ $model }}"
                   class="my-2 text-xs font-bold text-gray-600"
            >{{ $label }}</label>
            @error($model) <x-form.error class="pl-4">{{ $message }}</x-form.error> @enderror
        </div>
    @endisset
    {{ $slot }}
</div>
