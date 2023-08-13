@props(['light' => false])
<pre {{ $attributes->merge(['class' => 'p-4 text-xs rounded break-words whitespace-pre-wrap ' . ($light ? 'bg-slate-100 text-slate-800' : 'bg-slate-800 text-gray-100')]) }}
>{{ $slot }}</pre>
