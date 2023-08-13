<x-admin.layout>
    <x-slot name="headline">{{ $headline }}</x-slot>
    <x-admin.anchor-back href="{{ route('admin.integration') }}"/>
    <x-form.form>
        <x-grid-3>
            <x-blocks.card class="flex items-center justify-between">
                <x-typography.title>{{ $enabled ? 'Enabled' : 'Disabled' }}</x-typography.title>
                <x-form.toggle model="enabled" :value="$enabled"/>
            </x-blocks.card>
            <x-blocks.card class="flex items-center justify-between">
                <x-typography.title>Receive inventory</x-typography.title>
                <x-form.toggle model="receiveInventory" :value="$receiveInventory"/>
            </x-blocks.card>
            <x-blocks.card class="flex items-center justify-between">
                <x-typography.title>Distribute orders</x-typography.title>
                <x-form.toggle model="distributeOrder" :value="$distributeOrder"/>
            </x-blocks.card>
        </x-grid-3>
        <x-grid-2>
            <div>
                <x-form.text model="name"
                             label="Name"
                />
            </div>
        </x-grid-2>
    </x-form.form>
</x-admin.layout>
