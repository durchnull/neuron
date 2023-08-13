<x-admin.layout>
    <x-slot name="headline">{{ $headline }}</x-slot>
    <x-slot name="actions">
        <x-buttons.button wire:click="refreshAccessToken"
                          color="purple"
                          class="mx-2"
        >Refresh Access Token</x-buttons.button>
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
        </x-grid-3>
        <x-grid-2>
            <div>
                <x-form.text model="name"
                             label="Name"
                />
                <x-form.text model="clientId"
                             label="clientId"
                />
                <x-form.text model="clientSecret"
                             label="clientSecret"
                />
            </div>
            <div>
                <x-label class="block mt-4 mb-2">Access Token</x-label>
                <x-html.pre>{{ $accessToken }}</x-html.pre>
                @if ($accessTokenExpiresAt)
                    <x-label class="block mt-4 mb-2">Expires at</x-label>
                    <x-html.pre>{{ $accessTokenExpiresAt }} ({{ $accessTokenExpiresAt->diffForHumans() }})</x-html.pre>
                @endif
            </div>
        </x-grid-2>
    </x-form.form>
</x-admin.layout>
