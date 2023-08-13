@props(['json' => []])
@php
    $replace = [
        '"' => '<span class="text-blue-500">"</span>',
        '{' => '<span class="text-yellow-500">{</span>',
        '}' => '<span class="text-yellow-500">}</span>',
        '[' => '<span class="text-green-500">[</span>',
        ']' => '<span class="text-green-500">]</span>',
        ': ' => '<span class="text-yellow-500">: </span>',
        ',' => '<span class="text-gray-500">,</span>',
        '>id<' => '><span class="text-blue-300">id</span><',
        'null' => '<span class="text-red-400">null</span>',
    ];
    $json = json_encode($json, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);

    foreach ($replace as $from => $to) {
        $json = str_replace($from, $to, $json);
    }
@endphp
<x-html.pre {{ $attributes->merge(['class' => 'leading-5']) }}>{!! $json !!}</x-html.pre>
