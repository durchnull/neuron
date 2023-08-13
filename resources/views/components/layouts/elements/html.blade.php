<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
{{ $head }}
<x-layouts.elements.body>
    {{ $slot }}
    <x-layouts.elements.script/>
</x-layouts.elements.body>
</html>
