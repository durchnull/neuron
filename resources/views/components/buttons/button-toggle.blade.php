@props([
    'toggleAttribute' => $toggleAttribute ?? 'toggle',
    'iconTrue' => 'arrow-small-left',
    'iconFalse' => 'arrow-small-right'
])
<button type="button"
        class="p-2 bg-gray-50 rounded-full border hover:border-gray-400"
        @click="{{ $toggleAttribute }} = !{{ $toggleAttribute }}"
        {{ $attributes->merge(['class' => 'group cursor-pointer hover:border-gray-400']) }}
>
    <span x-show="{{ $toggleAttribute }}"
          @if ($toggleAttribute)
            x-cloak
          @endif
          class="block transform rotate-90"
    >@include('svg.' . $iconTrue)</span>
    <span x-show="!{{ $toggleAttribute }}"
          @if (!$toggleAttribute)
              x-cloak
          @endif
          class="block transform rotate-90"
    >@include('svg.' . $iconFalse)</span>
</button>
