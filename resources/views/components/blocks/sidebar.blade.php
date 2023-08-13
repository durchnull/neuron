<div {{ $attributes->merge(['class' => '
    fixed inset-0 transform -translate-x-full transition-transform bg-white
    lg:max-w-xs lg:w-full lg:ml-auto
    lg:transform-none lg:sticky top-0 lg:text-right lg:bg-transparent'
]) }}>
    @foreach($items as $item)
        @if (is_array($item))
            <ul class="bg-white border border-b-4 rounded-lg my-4 overflow-hidden">
                @foreach($item as $anchor)
                    <li>
                        <x-navigation.sidebar-anchor href="{{ route($anchor['route']) }}"
                                                     active="{{ \Illuminate\Support\Str::startsWith(Route::currentRouteName(), $anchor['route']) }}"
                                                     icon="{{ $anchor['icon'] }}"
                        >{{ $anchor['title'] }}
                        </x-navigation.sidebar-anchor>
                    </li>
                @endforeach
            </ul>
        @elseif(is_string($item))
            @if ($item === 'separator')
                <x-shapes.separator class="hidden lg:block lg:w-full my-8"/>
            @elseif($item === 'user')
                <div class="bg-white border border-b-4 border-gray-400 rounded-lg mb-4 overflow-hidden">
                    <ul>
                        <li class="flex items-center justify-between">
                            <x-navigation.sidebar-anchor href="{{ route('admin.user') }}"
                                                         active="{{ \Illuminate\Support\Str::startsWith(Route::currentRouteName(), 'admin.user') }}"
                                                         class="flex-1"
                            >{{ \Illuminate\Support\Facades\Auth::user()->name }}</x-navigation.sidebar-anchor>
                            {{--
                            <x-navigation.sidebar-anchor
                                href="{{ route('shop') }}"
                                class="text-xs"
                            >@include('svg.shopping-cart-solid')</x-navigation.sidebar-anchor>
                            --}}
                            <x-navigation.sidebar-anchor
                                href="{{ route('demo') }}"
                                class="text-xs"
                            >@include('svg.wrench-solid')</x-navigation.sidebar-anchor>
                            <x-form.presets.logout class="block p-5 text-xs hover:bg-gray-50">Logout</x-form.presets.logout>
                        </li>
                        <li>
                        </li>
                    </ul>

                    <div class="p-4">
                        <livewire:admin.sales-channel-select/>
                    </div>
                </div>
            @endif
        @endif
    @endforeach
</div>
