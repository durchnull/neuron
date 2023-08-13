@if ($value instanceof \App\Models\Integration\Integration)
    <x-admin.attributes.integration_provider :value="$value->getIntegrationProvider()" />
@else
    <span class="opacity-25">-</span>
@endif
