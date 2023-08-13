<x-admin.layout>
    <x-slot name="headline">{{ $headline }}</x-slot>
    <x-slot name="actions">
        @if ($receiveInventory)
            <x-buttons.button wire:click="syncInventory"
                              color="blue"
                              class="flex items-center mx-2"
            >
                <span wire:loading class="mx-2">
                    <x-spinner/>
                </span>
                Receive inventory
            </x-buttons.button>
        @endif
        <x-buttons.button wire:click="testIntegration"
                          color="{{ is_bool($integrationTest) ? ($integrationTest ? 'green' : 'red') : 'gray' }}"
        >{{ is_bool($integrationTest) ? ($integrationTest ? '✓' : '✗') : 'Test' }}</x-buttons.button>
    </x-slot>
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
                <x-form.text model="distributionChannel"
                             label="distributionChannel"
                />
                <x-form.text model="articleCategoryId"
                             label="articleCategoryId"
                />
            </div>
            <div>
                <x-form.text model="url"
                             label="Url"
                />
                <x-form.text model="apiToken"
                             label="Api token"
                />
            </div>
        </x-grid-2>
    </x-form.form>
</x-admin.layout>
