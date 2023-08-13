@if ($value instanceof \Illuminate\Database\Eloquent\Collection)
    <div class="flex">
        @foreach($value as $productPrice)
            <div class="{{ $loop->last ? '' : ' pr-4 mr-4 border-r' }} {{ $productPrice->end_at->isPast() ? 'text-gray-400' : '' }}">
                <x-parsing.price :amount="$productPrice->net_price"/>
                <div class="whitespace-nowrap">
                    @if ($productPrice->begin_at->isFuture())
                        <span class="text-xs">starts in</span>
                        <x-parsing.diff-for-humans :value="$productPrice->begin_at"/>
                    @elseif ($productPrice->end_at->isFuture())
                        <span class="text-xs">ends in</span>
                        <x-parsing.diff-for-humans :value="$productPrice->end_at"/>
                    @else
                        <span class="text-xs">ended</span>
                        <x-parsing.diff-for-humans :value="$productPrice->end_at"/>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif
