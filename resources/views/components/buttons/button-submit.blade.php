@props([
    'color' => 'blue',
    'type' => 'submit',
])
<x-buttons.button color="blue"
                  type="submit"
                    {{ $attributes }}
>{{ $slot }}</x-buttons.button>
