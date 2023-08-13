@props([
    'options' => []
])
<div {{ $attributes->merge(['class' => 'relative']) }}>
    <span class="absolute right-0 top-0 bottom-0 flex items-center justify-center h-12 px-4 pointer-events-none">
        @include('svg.bars-3')
    </span>
    <label>
        <select
            class="block w-full h-12 pl-4 pr-10 py-2 text-sm appearance-none border bg-white border-gray-300 text-gray-900 rounded-lg cursor-pointer hover:bg-gray-100 hover:border-gray-400 focus:ring-blue-500 focus:border-blue-500">
            <option disabled>Select...</option>
            @foreach($options as $option)
                <option
                    value="{{ $option['value'] }}"
                    @if (isset($option['selected']) && $option['selected'] === true)
                        selected
                    @endif
                >{{ $option['label'] }}</option>
            @endforeach
        </select>
    </label>
</div>
