<x-layouts.web>
    <div class="w-screen h-screen flex flex-col items-center justify-center">
        <x-navigation.logo/>
        <x-typography.headline class="mt-4">Neuron</x-typography.headline>
        <x-grid-2>
            <div>
                <x-label>Shops</x-label>
                @foreach($shops as $shop)
                    <x-blocks.card
                        class="hover:border-blue-500 cursor-pointer"
                    >
                        <a href="{{ $shop['url'] }}"
                           class="flex items-start justify-between h-20 "
                        >
                            <x-typography.small>{{ $shop['name'] }}</x-typography.small>
                            @include('svg.arrow-small-right')
                        </a>
                    </x-blocks.card>
                @endforeach
            </div>
            <div>
                <x-label>Login / Register</x-label>
                @auth()
                    <ul class="flex items-center font-mono my-8">
                        @if (\App\Models\Engine\Merchant::count() === 0)
                            <li class="m-2">
                                <x-navigation.button-anchor
                                    class="bg-white hover:bg-blue-50 shadow"
                                    icon="queue-list"
                                    href="{{ route('demo') }}"
                                >Demo
                                </x-navigation.button-anchor>
                            </li>
                        @else
                            <li class="m-2">
                                <x-navigation.button-anchor
                                    class="bg-white hover:bg-blue-50 shadow"
                                    icon="queue-list"
                                    href="{{ route('admin.home') }}"
                                >Admin
                                </x-navigation.button-anchor>
                            </li>
                        @endif
                    </ul>
                    <x-form.presets.logout class="text-xs font-bold hover:text-blue-500 my-8">Logout</x-form.presets.logout>
                @endauth
                @guest()
                    <x-form.presets.login/>
                @endguest
            </div>
        </x-grid-2>
    </div>
</x-layouts.web>
