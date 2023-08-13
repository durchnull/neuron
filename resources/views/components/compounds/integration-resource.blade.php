@props([
    'integrationType' => '',
    'status' => '',
    'createdAt' => '',
    'updatedAt' => '',
    'resourceId' => '',
    'resourceData' => [],
])
<div {{ $attributes->merge(['class' => '']) }}>
    <div class="flex flex-wrap items-center my-4">
        <x-admin.attributes.integration_provider
            :value="\Illuminate\Support\Str::kebab(class_basename($integrationType))"
        />
        <x-typography.title class="block mx-2 flex-1">{{ class_basename($integrationType) }}</x-typography.title>
        <x-admin.attributes.status :value="$status" class="my-2"/>
    </div>
    <x-compounds.timestamps
        :created_at="$createdAt"
        :updated_at="$updatedAt"
    />
    <x-html.pre class="text-xs break-all my-4">{{ $resourceId }}</x-html.pre>
    @if ($integrationType === \App\Models\Integration\PaymentProvider\Mollie::class)
        @isset($resourceData['payments'])
            @foreach($resourceData['payments'] as $payment)
                <x-html.pre class="flex items-center justify-between text-xs break-all my-4">
                    <span>{{ $payment['id'] }}</span>
                    <span>{{ $payment['status'] }}</span>
                </x-html.pre>
            @endforeach
        @endisset
    @else
        <x-html.pre-json :json="$resourceData" class="my-4">{{ $resourceId }}</x-html.pre-json>
    @endif
</div>
