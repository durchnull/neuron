<x-admin.layout>
    <x-slot name="headline">{{ $name }}</x-slot>
    <x-slot name="actions">
        @if($canDelete)
            <x-buttons.button
                wire:click="delete"
                color="red"
            >Delete
            </x-buttons.button>
        @endif
    </x-slot>
    <x-admin.anchor-back href="{{ route('admin.action-rules') }}"/>
    <x-form.form>
    </x-form.form>
</x-admin.layout>
