<x-admin.layout>
    <x-slot name="headline">{{ $headline }}</x-slot>
    <x-slot name="actions">
        <x-buttons.button wire:click="testIntegration"
                          color="{{ is_bool($integrationTest) ? ($integrationTest ? 'green' : 'red') : 'gray' }}"
        >{{ is_bool($integrationTest) ? ($integrationTest ? '✓' : '✗') : 'Test' }}</x-buttons.button>
    </x-slot>
    <x-admin.anchor-back href="{{ route('admin.integration') }}"/>
    <form wire:submit="save">
        <div class="my-4">
            <div class="grid grid-cols-2 gap-4">
                <x-blocks.card>
                    <div class="flex items-center justify-between my-4">
                        <x-typography.title class="flex-1">Account</x-typography.title>
                        <x-typography.small class="px-4 {{ $enabled ? 'text-green-500' : ''  }}">{{ $enabled ? 'enabled' : 'disabled'  }}</x-typography.small>
                        <x-form.toggle model="enabled" :value="$enabled"/>
                    </div>
                    <x-form.text model="name"
                                            label="Name"
                    />
                    <x-form.text model="from"
                                            label="From email"
                    />
                    <x-form.text model="sandboxTo"
                                            label="Sandbox to email"
                    />
                </x-blocks.card>
                <x-blocks.card>
                    <div class="flex items-center justify-between my-4">
                        <x-typography.title class="flex-1 h-8">Credentials</x-typography.title>
                    </div>
                    <x-form.text model="domain"
                                            label="Domain"
                    />
                    <x-form.text model="endpoint"
                                            label="Endpoint"
                    />
                    <div class="relative">
                        {{-- @todo [frontend] input-password --}}
                        <x-admin.attributes.password :value="$secret"
                                                     class="absolute bottom-0 left-0 ml-4 mb-2 bg-white"
                        />
                        <x-form.text model="secret"
                                                label="Secret"
                        />
                    </div>
                    <div class="relative">
                        <x-admin.attributes.password :value="$apiKey"
                                                     class="absolute bottom-0 left-0 ml-4 mb-2 bg-white"
                        />
                        <x-form.text model="apiKey"
                                                label="API Key"
                        />
                    </div>
                </x-blocks.card>
                <x-blocks.card>
                    <div class="flex items-center justify-between my-4">
                        <x-typography.title class="flex-1">Send order confirmation mail</x-typography.title>
                        <x-typography.small class="px-4 {{ $distributeOrder ? 'text-green-500' : ''  }}">{{ $distributeOrder ? 'enabled' : 'disabled'  }}</x-typography.small>
                        <x-form.toggle model="distributeOrder" :value="$distributeOrder"/>
                    </div>
                    <x-form.text model="orderTemplate"
                                            label="Template"
                    />
                    <x-form.text model="orderSubject"
                                            label="Subject"
                    />
                </x-blocks.card>
                <x-blocks.card>
                    <div class="flex items-center justify-between my-4">
                        <x-typography.title class="flex-1">Send refunding mail</x-typography.title>
                        <x-typography.small class="px-4 {{ $refundOrder ? 'text-green-500' : ''  }}">{{ $refundOrder ? 'enabled' : 'disabled'  }}</x-typography.small>
                        <x-form.toggle model="refundOrder" :value="$refundOrder"/>
                    </div>
                    <x-form.text model="refundTemplate"
                                            label="Template"
                    />
                    <x-form.text model="refundSubject"
                                            label="Subject"
                    />
                </x-blocks.card>
                <x-blocks.card>
                    <x-typography.title class="block text-gray-400 mb-4">Inactivity mail</x-typography.title>
                </x-blocks.card>
            </div>
        </div>
        <div wire:dirty class="w-full text-center my-8">
            <x-buttons.button-submit class="max-w-xs w-full">Save</x-buttons.button-submit>
        </div>
    </form>
</x-admin.layout>
