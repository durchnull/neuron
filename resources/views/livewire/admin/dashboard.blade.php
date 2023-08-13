<x-admin.layout>
    <x-slot name="headline">Dashboard</x-slot>
    <x-grid-3>
        <x-blocks.card class="col-span-2">
            <x-grid-3>
                <div>
                    <x-typography.title class="block text-gray-400 mb-4">Orders</x-typography.title>
                    <x-typography.big-text class="block my-4">{{ $orderCount }}</x-typography.big-text>
                    <x-admin.attributes.money :value="$orderRevenue ?? 0"/>
                </div>
                <div>
                    <x-typography.title class="block text-gray-400 mb-4">Carts</x-typography.title>
                    <x-typography.big-text class="block my-4">{{ $cartCount }}</x-typography.big-text>
                    <x-admin.attributes.money :value="$cartRevenue ?? 0"/>
                </div>
                <div>
                    <x-typography.title class="block text-gray-400 mb-4">Active today</x-typography.title>
                    <x-typography.big-text class="block my-4">{{ $cartActiveTodayCount }}</x-typography.big-text>
                </div>
            </x-grid-3>
        </x-blocks.card>
        <x-blocks.card class="col-span-1">
            <x-grid-2>
                <div>
                    <x-typography.title class="block text-gray-400 mb-4">Products</x-typography.title>
                    <x-typography.big-text class="block my-4">{{ $productCount }}</x-typography.big-text>
                </div>
                <div>
                    <x-typography.title class="block text-gray-400 mb-4">Queue</x-typography.title>
                    <x-typography.big-text class="block my-4">{{ $stockQueueSum }}</x-typography.big-text>
                </div>
            </x-grid-2>
        </x-blocks.card>
        <x-blocks.card class="col-span-2">
            <x-grid-3>
                <div>
                    <x-typography.title class="block text-gray-400 mb-4">Promotions</x-typography.title>
                    <x-typography.big-text class="block my-4">{{ $cartRules->count() }}</x-typography.big-text>
                </div>
                <div>
                    <x-typography.title class="block text-gray-400 mb-4">Coupons</x-typography.title>
                    <x-typography.big-text class="block my-4">{{ $couponCount }}</x-typography.big-text>
                </div>
                <div>
                    <x-typography.title class="block text-gray-400 mb-4">Policies</x-typography.title>
                    <x-typography.big-text class="block my-4">{{ $actionRuleCount }}</x-typography.big-text>
                </div>
            </x-grid-3>
        </x-blocks.card>
        <x-blocks.card class="col-span-1">
            <x-grid-2>
                <div>
                    <x-typography.title class="block text-gray-400 mb-4">Customers</x-typography.title>
                    <x-typography.big-text>{{ $customerCount }}</x-typography.big-text>
                </div>
                <div>
                    <x-typography.title class="block text-gray-400 mb-4">New</x-typography.title>
                    <x-typography.big-text>{{ $newCustomerCount }}</x-typography.big-text>
                </div>
            </x-grid-2>
        </x-blocks.card>
    </x-grid-3>
    <div>
        <x-blocks.card class="my-8">
            <x-compounds.timeline
                :begin="now()"
                :end="now()->addWeeks(2)"
                :events="$timelineEvents"
            />
        </x-blocks.card>
    </div>
    <div>
        @foreach($cartRules as $cartRule)
            <x-blocks.card class="my-8">
                <x-compounds.cart-rule :cartRule="$cartRule"/>
            </x-blocks.card>
        @endforeach
    </div>
    <x-blocks.card class="col-span-3">
        <x-typography.title class="block text-gray-400 mb-4">Integrations</x-typography.title>
        <div class="flex flex-wrap">
            @foreach($integrations as $integration)
                <div class="flex items-center m-1 bg-gray-50 px-6 py-4 rounded">
                    <x-admin.attributes.integration_provider :value="$integration['name']"/>
                    <x-typography.small class="mx-2">{{ $integration['name'] }}</x-typography.small>
                </div>
            @endforeach
        </div>
    </x-blocks.card>
</x-admin.layout>
