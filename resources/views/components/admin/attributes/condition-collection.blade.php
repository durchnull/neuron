<div class="inline-flex items-center bg-gray-100 rounded font-mono text-xs whitespace-nowrap">
    @foreach($value->getElements() as $element)
        @if ($element instanceof \App\Condition\Condition)
            <div class="relative inline-flex items-center">
                <div class="p-4 rounded font-bold">{{ $element->getProperty()->getType()->value }}</div>
                <div class="p-4 rounded bg-gray-200">{{ $element->getComparison()->getType()->value }}</div>
                <div class="p-4 rounded">
                    @if ($element->getProperty()->getType() === \App\Condition\PropertyTypeEnum::DateTime)
                        <x-parsing.date-time :value="$element->getValue()->get()"/>
                    @else
                        {{ $element->getValue()->get() }}
                    @endif
                </div>
            </div>
        @elseif($element instanceof \App\Condition\Operator)
            <div class="p-4 bg-gray-600 rounded text-white">{{ $element->getType() }}</div>
        @else
            <div class="flex items-center">
                <div class="px-4 text-gray-500 text-4xl">(</div>
                <x-admin.attributes.condition-collection :conditionCollection="$element"/>
                <div class="px-4 text-gray-500 text-4xl">)</div>
            </div>
        @endif
    @endforeach
</div>
