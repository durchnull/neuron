<x-blocks.card>
    <div class="flex items-center justify-between">
        <x-label class="mr-8">Transaction</x-label>
        <x-admin.attributes.status :value="\App\Enums\Transaction\TransactionStatusEnum::from($transaction['status'])"/>
    </div>
    <x-typography.small class="block my-4">{{ $transaction['resource_id'] }}</x-typography.small>
    @if (!empty($transaction['checkout_url']))
        <div class="my-4">
            <x-typography.small class="block">{{ $transaction['checkout_url'] }}</x-typography.small>
            <x-navigation.button-anchor
                href="{{ $transaction['checkout_url'] }}"
                class="my-4"
            >Checkout</x-navigation.button-anchor>
        </div>
    @endif
</x-blocks.card>
