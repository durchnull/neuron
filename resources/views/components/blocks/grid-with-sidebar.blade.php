<div class="grid grid-cols-1 lg:grid-cols-5 gap-4 p-4">
    <div class="lg:col-span-1">
        {{ $sidebar }}
    </div>
    <div class="lg:col-span-4">
        <div class="p-8 bg-white rounded-lg shadow-2xl">
            <div class="flex items-center justify-between">
                @isset($headline)
                    <x-typography.headline class="mt-3">{{ $headline }}</x-typography.headline>
                @endisset
                @isset($actions)
                    <div class="flex items-center justify-between">
                        {{ $actions }}
                    </div>
                @endisset
            </div>
            {{ $slot }}
        </div>
    </div>
</div>
