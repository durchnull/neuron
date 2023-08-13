<div class="flex items-center">
    @foreach($consequenceCollection->getConsequences() as $consequence)
        <div class="inline-flex items-center px-4 py-4 rounded bg-green-100 text-green-700 font-bold text-xs">
            @if ($consequence instanceof \App\Consequence\AddItem)
                <div class="whitespace-nowrap">Add {{ $consequence->getQuantity() }}
                    x {{ \App\Models\Engine\Product::where('id', $consequence->getProductId())->value('name') }} <div class="text-xs">{{ $consequence->getReference() }}</div></div>
            @elseif ($consequence instanceof \App\Consequence\Discount)
                @if ($consequence->isPercentage())
                    <div class="whitespace-nowrap">{{ $consequence->getAmount() }} %</div>
                @else
                    <x-admin.attributes.money :value="$consequence->getAmount()"/>
                @endif
                <div class="pl-2 whitespace-nowrap">discount on
                    @foreach($consequence->getTargets() as $target)
                        <span>{{ __('consequences.targets.' . $target[0]) }}</span>
                    @endforeach
                </div>
            @endif
        </div>
        @if (!$loop->last)
            <div class="mx-4 text-blue-500 text-xl font-bold">+</div>
        @endif
    @endforeach
</div>
