<x-admin.layout>
    <x-slot name="headline">Integrations</x-slot>
    <x-admin.table-list
        :tableAttributes="$tableAttributes"
        :models="$models"
        :resourceRoute="$resourceRoute"
    />
</x-admin.layout>
