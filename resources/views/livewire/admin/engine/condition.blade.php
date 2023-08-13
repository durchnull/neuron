<x-admin.layout>
    <x-slot name="headline">{{ $name }}</x-slot>
    <x-admin.anchor-back href="{{ route('admin.conditions') }}"/>
    <div class="py-8">
        <x-admin.attributes.condition-collection :value="\App\Condition\ConditionCollection::fromArray($collection)"/>
    </div>
</x-admin.layout>
