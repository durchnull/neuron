@props([
    'tableAttributes' => [],
    'models' => [],
    'resourceRoute' => ''
])
<x-html.table class="py-8">
    <thead>
    <tr>
        @foreach($tableAttributes as $tableAttribute => $view)
            <x-html.th class="{{ $view === 'money' && !$loop->first ? 'text-right' : '' }}"
            >{{ __('admin.attributes.' . $tableAttribute) }}</x-html.th>
        @endforeach
        <x-html.th>Actions</x-html.th>
    </tr>
    </thead>
    <tbody class="font-mono">
    @foreach($models as $model)
        @php
            $trEven = $loop->even;
            $modelRoute = $resourceRoute . '.' . str_replace('app.models.', '', str_replace('\\-', '.', \Illuminate\Support\Str::kebab(get_class($model))));
        @endphp
        <tr class="group">
            @foreach($tableAttributes as $tableAttribute => $view)
                @php $componentName = 'admin.attributes.' . $view @endphp
                <x-html.td
                    class="group-hover:bg-gray-50 transition-colors {{ $trEven ? '' : '' }} {{ $view === 'money' ? 'text-right' : '' }}"
                >
                    <x-dynamic-component :component="$componentName" :value="$model->getAttribute($tableAttribute)">
                        @if (!is_array($model->getAttribute($tableAttribute)) && !is_object($model->getAttribute($tableAttribute)))
                            {{ $model->getAttribute($tableAttribute) }}
                        @endif
                    </x-dynamic-component>
                </x-html.td>
            @endforeach
            <x-html.td class="group-hover:bg-gray-50 transition-colors {{ $trEven ? '' : '' }}">
                @if (\Illuminate\Support\Facades\Route::has($modelRoute))
                    <x-navigation.anchor
                        href="{{ route($modelRoute, ['id' => $model->id]) }}"
                        icon="eye"
                        wire:navigate
                    >View
                    </x-navigation.anchor>
                @else
                    <x-navigation.anchor
                        href="{{ route('admin.engine.resource', ['id' => $model->id, 'class' => get_class($model)]) }}"
                        icon="eye"
                        wire:navigate
                    >
                        <span class="text-xs text-gray-400">{{ $modelRoute ?? 'none' }}</span>
                    </x-navigation.anchor>
                @endif
            </x-html.td>
        </tr>
    @endforeach
    </tbody>
</x-html.table>
