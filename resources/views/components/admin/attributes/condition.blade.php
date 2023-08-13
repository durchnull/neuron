@if ($value instanceof \App\Models\Engine\Condition)
    <div class="relative whitespace-nowrap">
        <span class="block">{{ $value->name }}</span>
        <div class="fixed z-10 opacity-0 group-hover:opacity-100 pointer-events-none">
            <x-blocks.card>
                <x-html.pre-json :json="$value->collection->toArray()"/>
            </x-blocks.card>
        </div>
    </div>
@else
    <span class="opacity-25">-</span>
@endif
