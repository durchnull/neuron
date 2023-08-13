@if ($value instanceof \App\Models\Engine\Rule)
    <div class="relative whitespace-nowrap">
        <span class="block">{{ $value->name }}</span>
        <div class="fixed left-0 top-0 ml-4 z-10 opacity-0 group-hover:opacity-100 pointer-events-none">
            <x-blocks.card>
                <x-typography.small class="block p-4"><span class="font-bold">IF</span> {{ $value->condition->name }}</x-typography.small>
                <x-html.pre-json :json="$value->condition->collection->toArray()"/>
                <x-typography.small class="block p-4"><span class="font-bold">THEN APPLY</span> {{ $value->name }}</x-typography.small>
                <x-html.pre-json :json="$value->consequences->toArray()"/>
            </x-blocks.card>
        </div>
    </div>
@else
    <span class="opacity-25">-</span>
@endif
