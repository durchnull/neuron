<form wire:submit="save">
    {{ $slot }}
    <div wire:dirty>
        <x-buttons.button-submit
            class="max-w-xs w-full"
        >Save</x-buttons.button-submit>
    </div>
</form>
