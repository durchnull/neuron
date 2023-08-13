<x-blocks.grid-with-sidebar>
    <x-slot name="sidebar">
        <x-admin.sidebar/>
    </x-slot>
    @isset($headline)
        <x-slot name="headline">{{ $headline }}</x-slot>
    @endisset
    @isset($actions)
        <x-slot name="actions">{{ $actions }}</x-slot>
    @endisset
    {{ $slot }}
</x-blocks.grid-with-sidebar>
