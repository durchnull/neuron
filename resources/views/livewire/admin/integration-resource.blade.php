<div x-data="{ toggle : false }">
    <div>
        <x-buttons.button-toggle/>
    </div>
    <div x-show="toggle"
         x-cloak
         class="fixed inset-0 z-10 overflow-scroll opacity-95"
    >
        <div class="sticky top-0 z-10 p-4 flex items-center justify-center bg-white">
            <x-buttons.button-toggle/>
        </div>
        <div class="m-2 mt-0">
            <x-html.pre-json :json="$resource"/>
        </div>
        <div class="m-2">
            <x-html.pre-json :json="$customerProfile"/>
        </div>
    </div>
</div>
