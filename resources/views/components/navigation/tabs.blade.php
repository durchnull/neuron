@props([
    'tabs' => [],
    'id' => ''
])
@if (!empty($tabs))
    <div x-data="{ tab : $persist(1).as('tabs-{{ $id }}') }"
         class="py-4"
    >
        <div class="grid grid-cols-{{ count($tabs) }} gap-4">
            @foreach($tabs as $tab)
                <x-buttons.button-tab
                    tab="{{ $loop->index + 1 }}"
                >{{ $loop->index + 1 }} {{ $tab }}</x-buttons.button-tab>
            @endforeach
        </div>
        <div class="">
            {{ $slot }}
        </div>
    </div>
@endif
