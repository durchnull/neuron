@props([
    'color' => 'gray-light'
])
@php
    switch ($color) {
        case 'green':
        case 'red':
        case 'yellow':
        case 'blue':
        case 'gray':
        default:
            $textColor = 'text-white';
            $bgColor = 'bg-' . $color . '-600';
            $borderColor = 'border-' . $color . '-600';
            $hoverBgColor = 'hover:bg-' . $color . '-700';
            $hoverBorderColor = 'hover:border-' . $color . '-700';
            break;
        case 'black':
            $textColor = 'text-white';
            $bgColor = 'bg-gray-800';
            $borderColor = 'border-gray-800';
            $hoverBgColor = 'hover:bg-gray-900';
            $hoverBorderColor = 'hover:border-gray-900';
            break;
        case 'gray-light':
            $textColor = 'text-gray-800';
            $bgColor = 'bg-gray-200';
            $borderColor = 'border-gray-200';
            $hoverBgColor = 'hover:bg-gray-300';
            $hoverBorderColor = 'hover:border-gray-300';
            break;
    }

    $colors = implode(' ', [$textColor, $bgColor, $borderColor, $hoverBgColor, $hoverBorderColor]);
@endphp
<button type="{{ $attributes->get('type') ?? 'button' }}"
    {{ $attributes->merge(['class' => 'px-4 py-2 border rounded-full font-bold cursor-pointer transition-colors ' . $colors]) }}
>{{ $slot }}</button>
