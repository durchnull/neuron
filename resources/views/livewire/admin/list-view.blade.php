<x-admin.layout>
    <x-slot name="headline">{{ $headline }}</x-slot>
    <x-admin.search/>
    <x-admin.table-list
        :tableAttributes="$tableAttributes"
        :models="$models"
        :resourceRoute="$resourceRoute"
    />
    <div class="p-8">
        {{ $models->links(data: ['scrollTo' => false]) }}
    </div>
</x-admin.layout>
