<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @if (true)
        <script src="https://cdn.tailwindcss.com"></script>
        {{--
         <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
         --}}
        @vite('resources/js/app.js')
    @else
        <script>console.log('head:local')</script>
        <link rel="stylesheet" href="/tailwind.min.css">
        <style>.bg-slate-800{background-color: black;}</style>
    @endif
    @yield('head')
</head>
