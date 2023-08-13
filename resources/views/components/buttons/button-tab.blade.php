<button type="button"
        @click="tab = {{ $tab }}"
        class="px-4 py-2 border-2 rounded-full font-bold cursor-pointer transition-colors"
        x-bind:class="{
            'text-blue-600 border-blue-400 bg-blue-50' : tab == {{ $tab }},
            'text-gray-600 hover:border-gray-400' : tab != {{ $tab }},
        }"
>{{ $slot }}</button>
