<x-admin.layout>
    <x-slot name="headline">{{ $modelAttributes['name'] ?? $modelAttributes['id'] }}</x-slot>
    <x-admin.anchor-back href="{{ route('admin.dashboard') }}"/>
    <div class="my-8 grid grid-cols-2 gap-4">
        @foreach($modelAttributes as $key => $modelAttribute)
            <div class="overflow-hidden">
                <x-label class="block">{{ $key }}</x-label>
                <x-typography.big-text class="break-words whitespace-pre-wrap">{{ $modelAttribute }}</x-typography.big-text>
            </div>
        @endforeach
    </div>
</x-admin.layout>
