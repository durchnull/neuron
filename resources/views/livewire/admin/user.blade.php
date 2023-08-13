<x-admin.layout>
    <x-slot name="headline">{{ \Illuminate\Support\Facades\Auth::user()->name }}</x-slot>
    <x-grid-3 class="my-8">
        <div>
            <x-label class="block">Email</x-label>
            <x-typography.big-text>{{ \Illuminate\Support\Facades\Auth::user()->email }}</x-typography.big-text>
        </div>
        <div>
            <x-label class="block">Merchant</x-label>
            <x-typography.big-text>{{ \Illuminate\Support\Facades\Auth::user()->merchant->name }}</x-typography.big-text>
        </div>
        <div>
            <x-label class="block">Verified at</x-label>
            <x-parsing.date-time :value="\Illuminate\Support\Facades\Auth::user()->email_verified_at"/>
            <br>
            <x-parsing.diff-for-humans :value="\Illuminate\Support\Facades\Auth::user()->email_verified_at"/>
        </div>
    </x-grid-3>
    <x-shapes.separator/>
    <x-grid-3 class="my-8">
        @foreach (\Illuminate\Support\Facades\Auth::user()->tokens as $token)
            <div>
                <x-label class="block mb-2">Token {{ $token->id }}</x-label>
                <x-html.pre-json :json="$token"/>
            </div>
        @endforeach
    </x-grid-3>
</x-admin.layout>
