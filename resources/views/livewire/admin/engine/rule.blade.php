<x-admin.layout>
    <x-slot name="headline">{{ $rule->name }}</x-slot>
    <x-admin.anchor-back href="{{ route('admin.rules') }}"/>
    <div class="py-8">
        <x-blocks.card>
            <x-parsing.condition-collection :conditionCollection="$rule->condition->collection"/>
        </x-blocks.card>
        <x-blocks.card>
            <x-parsing.consequence-collection :consequenceCollection="$rule->consequences"/>
        </x-blocks.card>
    </div>
</x-admin.layout>
