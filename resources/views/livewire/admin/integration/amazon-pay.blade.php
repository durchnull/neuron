<x-admin.layout>
    <x-slot name="headline">{{ $headline }}</x-slot>
    <x-slot name="actions">
        <x-buttons.button wire:click="testIntegration"
                          color="{{ is_bool($integrationTest) ? ($integrationTest ? 'green' : 'red') : 'gray' }}"
        >{{ is_bool($integrationTest) ? ($integrationTest ? '✓' : '✗') : 'Test' }}</x-buttons.button>
    </x-slot>
    <x-admin.anchor-back href="{{ route('admin.integration') }}"/>
    <x-form.form>
        <x-grid-3>
            <div>
                <x-blocks.card class="flex items-center justify-between">
                    <x-typography.title>{{ $enabled ? 'Enabled' : 'Disabled' }}</x-typography.title>
                    <x-form.toggle model="enabled" :value="$enabled"/>
                </x-blocks.card>
            </div>
        </x-grid-3>
        <x-grid-2>
            <div>
                <x-form.text model="name"
                             label="Name"
                />
                <x-form.text model="merchantAccountId"
                             label="merchantAccountId"
                />
                <x-form.text model="region"
                             label="region"
                />
                <x-form.text model="storeId"
                             label="storeId"
                />
                <x-form.text model="sandbox"
                             label="sandbox"
                />
            </div>
            <div>
                <x-form.text model="publicKeyId"
                             label="publicKeyId"
                />
                <x-form.textarea model="privateKey"
                                 label="privateKey"
                />
            </div>
        </x-grid-2>
    </x-form.form>
</x-admin.layout>
