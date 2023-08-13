{{-- @todo --}}
@php
    $_apiRoutes = array_filter(
        \Illuminate\Support\Facades\Route::getRoutes()->getRoutes(),
        fn(\Illuminate\Routing\Route $route) => \Illuminate\Support\Str::startsWith($route->getName(), 'api')
    );
    $apiRoutes = [];

    foreach ($_apiRoutes as $apiRoute) {
        $resource = \Illuminate\Support\Str::before(str_replace('api.', '', $apiRoute->getName()), '.');
        $apiRoutes[$resource][] = $apiRoute;
    }
@endphp
<x-layouts.web>
    <x-blocks.grid-with-sidebar>
        <x-slot name="sidebar">
            <div class="sticky top-0 p-4">
                <x-label>Documentation</x-label>
                <x-typography.headline>API</x-typography.headline>
                <ul>
                    @foreach($apiRoutes as $resource => $_apiRoutes)
                        <li>
                            <a href="#{{ $resource }}"
                               class="block py-1 font-bold hover:text-blue-500"
                            >{{ \Illuminate\Support\Str::studly($resource) }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </x-slot>
        <div class="my-8">
            @foreach($apiRoutes as $resource => $_apiRoutes)
                <div x-data="{ toggle: false }"
                     class="my-8"
                >
                    <x-typography.headline id="{{ $resource }}"
                                           class="pt-4"
                    >{{ \Illuminate\Support\Str::studly($resource) }}</x-typography.headline>
                    @foreach($_apiRoutes as $apiRoute)
                        <div class="flex items-center justify-between mb-4 mt-8 mx-2 font-mono text-xs">

                            <x-shapes.pill>{{ implode('', $apiRoute->methods()) }}</x-shapes.pill>
                            <x-shapes.separator class="w-10 mx-4"/>
                            <span class="font-bold">/{{ $apiRoute->uri }}</span>

                            <x-shapes.separator class="flex-1 mx-4"/>
                            <x-buttons.button-toggle/>
                        </div>
                        @if (in_array($apiRoute->getActionMethod(), ['create', 'update']))
                            @php $createActionClass = '\App\Actions\Engine\\' . \Illuminate\Support\Str::studly($resource) . '\\' . \Illuminate\Support\Str::studly($resource) . \Illuminate\Support\Str::studly($apiRoute->getActionMethod()) . 'Action'; @endphp
                            @if (class_exists($createActionClass))
                                <x-blocks.card>
                                    @foreach($createActionClass::rules() as $attribute => $validations)
                                        <div class="">
                                            @if (is_string($validations) && \Illuminate\Support\Str::contains($validations, 'required'))
                                                <div class="text-xs text-blue-500">required</div>
                                            @endif
                                            <div class="flex items-center justify-between">
                                                <x-typography.title>{{ $attribute }}</x-typography.title>
                                                <div class="font-mono text-xs">{{ json_encode($validations) }}</div>
                                            </div>
                                        </div>
                                        @if (!$loop->last)
                                            <x-shapes.separator/>
                                        @endif
                                    @endforeach
                                </x-blocks.card>
                            @else
                                <div class="text-red-500">Class does not exist {{ $createActionClass }}</div>
                            @endif
                        @else
                            <x-blocks.card>
                                {{ json_encode($apiRoute->getActionMethod()) }}
                            </x-blocks.card>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
    </x-blocks.grid-with-sidebar>
</x-layouts.web>
