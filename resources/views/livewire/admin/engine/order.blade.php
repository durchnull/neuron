@php /** @var \App\Models\Engine\Order $order */ @endphp
<x-admin.layout>

    {{-- Header --}}

    <x-slot name="headline">
        <span class="flex items-center">
            <span class="mr-2">{{ $order->order_number }}</span>
            <x-admin.attributes.status :value="$order->status"
                                       class="mx-2"
            />
            <x-shapes.pill class="text-xs font-normal">{{ $order->id }}</x-shapes.pill>
        </span>
    </x-slot>
    <x-slot name="actions">
        @if($canRefund)
            <x-buttons.button
                wire:click="refund"
                color="purple"
            >Refund
            </x-buttons.button>
        @endif
        @if($canClose)
            <x-buttons.button
                wire:click="close"
                color="gray"
            >Close
            </x-buttons.button>
        @endif
    </x-slot>
    <x-admin.anchor-back href="{{ route('admin.orders') }}"/>

    <div class="my-4">
        <x-grid-3>

            {{-- Summary --}}

            <x-blocks.card>
                <div class="mb-4">
                    <x-label class="block">Totals</x-label>
                    <x-typography.big-text>
                        <div class="inline-block text-right">
                            <x-parsing.price :amount="$order->amount"/>
                            @if ($order->items_discount_amount + $order->shipping_discount_amount)
                                <span class="block text-green-600">
                                -
                                <x-parsing.price
                                    :amount="$order->items_discount_amount + $order->shipping_discount_amount"/>
                            </span>
                            @endif
                        </div>
                    </x-typography.big-text>
                </div>
                <div class="my-4">
                    <x-label class="block">Cart</x-label>
                    <div class="inline-block text-right">
                        <x-parsing.price :amount="$order->items_amount - $order->items_discount_amount"/>
                        @if ($order->items_discount_amount)
                            <span class="block text-green-600">
                                -
                                <x-parsing.price :amount="$order->items_discount_amount"/>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="my-4">
                    <x-label class="block">Shipping</x-label>
                    <div class="inline-block text-right">
                        <x-parsing.price :amount="$order->shipping_amount - $order->shipping_discount_amount"/>
                        @if ($order->shipping_discount_amount)
                            <span class="block text-green-600">
                                -
                                <x-parsing.price :amount="$order->shipping_discount_amount"/>
                            </span>
                        @endif
                    </div>
                </div>
            </x-blocks.card>

            {{-- Details --}}

            <x-blocks.card>
                @if ($order->ordered_at)
                    <div class="mb-4">
                        <x-label class="block">Details</x-label>
                        <x-typography.big-text class="block">
                            {{ $order->ordered_at->format('d.m.Y') }}
                            <x-typography.small>{{ $order->ordered_at->format('H:i:s') }}</x-typography.small>
                        </x-typography.big-text>
                    </div>
                @endif
                <div class="my-4">
                    <x-label class="block">Payment</x-label>
                    <span>{{ $order->payment->method }}</span>
                </div>
                <div class="my-4">
                    <x-label class="block">Shipping</x-label>
                    <span>{{ $order->shipping->name }}</span>
                </div>
                @if ($order->coupons->isNotEmpty())
                    <div class="my-4">
                        <x-label class="block mb-2">Coupons</x-label>
                        @foreach($order->coupons as $coupon)
                            <x-parsing.coupon-code :code="$coupon->code"/>
                        @endforeach
                    </div>
                @endif
                @if ($order->cartRules->isNotEmpty())
                    <div class="my-4">
                        <x-label class="block mb-2">Cart Rules</x-label>
                        @foreach($order->cartRules as $cartRule)
                            <span>{{ $cartRule->name }}</span>
                        @endforeach
                    </div>
                @endif
                <x-compounds.timestamps
                    :created_at="$order->created_at"
                    :updated_at="$order->updated_at"
                />
            </x-blocks.card>

            {{-- Customer --}}

            @if ($order->customer)
                <x-blocks.card>
                    <div>
                        <x-label class="block">Customer</x-label>
                        <x-typography.headline class="flex">
                            {{ $order->customer->full_name }}
                            @if ($order->customer->new)
                                <span class="text-xs text-green-600 ml-1">new</span>
                            @endif
                        </x-typography.headline>
                    </div>
                    <div class="my-4">
                        <x-label class="block">Email</x-label>
                        <div>{{ $order->customer->email }}</div>
                    </div>
                    @if ($order->billingAddressIsShippingAddress())
                        <div class="my-4">
                            <x-label class="block">Address</x-label>
                            <div>{{ $order->shippingAddress->label() }}</div>
                        </div>
                    @else
                        <div class="my-4">
                            <x-label class="block">Billing address</x-label>
                            <div>{{ $order->billingAddress->label() }}</div>
                        </div>
                        <div class="my-4">
                            <x-label class="block">Shipping address</x-label>
                            <div>{{ $order->billingAddress->label() }}</div>
                        </div>
                    @endif
                    @if (!empty($order->customer_note))
                        <div class="my-4">
                            <x-label class="block">Note</x-label>
                            <div>{{ $order->customer_note }}</div>
                        </div>
                    @endif
                </x-blocks.card>
            @endif

            {{-- Items --}}

            <div class="col-span-3">
                <x-grid-3>
                    @foreach($order->items as $item)
                        <div class="flex items-center p-8">
                            <div class="relative">
                                <x-image.product-image :src="$item->product->image_url"/>
                                <x-shapes.circle
                                    class="absolute transform translate-x-1/3 -translate-y-1/3 top-0 right-0 text-xs font-bold bg-white border shadow"
                                >{{ $item->quantity }}</x-shapes.circle>
                            </div>
                            <div class="ml-6">
                                <x-typography.title class="block">{{ $item->product->name }}</x-typography.title>
                                <div class="text-right">
                                    <x-parsing.price :amount="$item->total_amount - $item->discount_amount"/>
                                    @if ($item->discount_amount)
                                        <div class="block text-green-600">
                                            -
                                            <x-parsing.price :amount="$item->discount_amount"/>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </x-grid-3>
            </div>
        </x-grid-3>
    </div>

    {{-- Status --}}

    <x-blocks.card class="flex items-center justify-between my-4 p-4">
        @foreach(\App\Enums\Order\OrderStatusEnum::cases() as $orderStatusEnum)
            @if (!$loop->first)
                <x-shapes.separator class="border-gray-200 flex-1 mx-3"/>
            @endif
            <div class="flex flex-col">
                @if ($order->status === $orderStatusEnum)
                    <x-admin.attributes.status :value="$orderStatusEnum"/>
                @else
                    <x-typography.small class="opacity-50">{{ $orderStatusEnum->value }}</x-typography.small>
                @endif
                @if (!empty($orderEvents))
                    @switch($orderStatusEnum)
                        @case(\App\Enums\Order\OrderStatusEnum::Open)
                            @php $orderEventCreatedAt = optional(collect($orderEvents)->first(fn(object $orderEvent) => $orderEvent->action === class_basename(\App\Actions\Engine\Order\OrderCreateAction::class)))->created_at @endphp
                            @break
                        @case(\App\Enums\Order\OrderStatusEnum::Placing)
                            @php $orderEventCreatedAt = optional(collect($orderEvents)->first(fn(object $orderEvent) => $orderEvent->action === class_basename(\App\Actions\Engine\Order\OrderPlaceAction::class)))->created_at @endphp
                            @break
                        @case(\App\Enums\Order\OrderStatusEnum::Accepted)
                            @php $orderEventCreatedAt = optional(collect($orderEvents)->first(
                                    fn(object $orderEvent) => $orderEvent->action === class_basename(\App\Actions\Engine\Order\OrderUpdateStatusAction::class)
                                        && json_decode($orderEvent->data, true)['status'] === \App\Enums\Order\OrderStatusEnum::Accepted->value
                                ))->created_at @endphp
                            @break
                        @case(\App\Enums\Order\OrderStatusEnum::Confirmed)
                            @php $orderEventCreatedAt = optional(collect($orderEvents)->first(
                                fn(object $orderEvent) => $orderEvent->action === class_basename(\App\Actions\Engine\Order\OrderUpdateStatusAction::class)
                                    && json_decode($orderEvent->data, true)['status'] === \App\Enums\Order\OrderStatusEnum::Confirmed->value
                            ))->created_at @endphp
                            @break
                        @case(\App\Enums\Order\OrderStatusEnum::Shipped)
                            @php $orderEventCreatedAt = optional(collect($orderEvents)->first(fn(object $orderEvent) => $orderEvent->action === class_basename(\App\Actions\Engine\Order\OrderShipAction::class)))->created_at @endphp
                            @break
                        @case(\App\Enums\Order\OrderStatusEnum::Refunded)
                            @php $orderEventCreatedAt = optional(collect($orderEvents)->first(fn(object $orderEvent) => $orderEvent->action === class_basename(\App\Actions\Engine\Order\OrderRefundAction::class)))->created_at @endphp
                            @break
                        @case(\App\Enums\Order\OrderStatusEnum::Canceled)
                            @php $orderEventCreatedAt = optional(collect($orderEvents)->first(fn(object $orderEvent) => $orderEvent->action === class_basename(\App\Actions\Engine\Order\OrderCancelAction::class)))->created_at @endphp
                            @break
                        @default
                            @php $orderEventCreatedAt = null @endphp
                            @break
                    @endswitch
                    @if ($orderEventCreatedAt)
                        <x-parsing.date-time :value="$orderEventCreatedAt" class="mt-2"/>
                        <x-parsing.diff-for-humans :value="$orderEventCreatedAt" class="mt-2"/>
                    @endif
                @endif
            </div>
        @endforeach
    </x-blocks.card>

    {{-- Transactions and integrations --}}

    <div class="my-4">
        <x-grid-3>
            @if($order->transactions->isNotEmpty())
                @foreach($order->transactions as $transaction)
                    <x-blocks.card>
                        <x-compounds.integration-resource
                            :integrationType="$transaction->integration_type"
                            :status="$transaction->status"
                            :createdAt="$transaction->created_at"
                            :updatedAt="$transaction->updated_at"
                            :resourceId="$transaction->resource_id"
                            :resourceData="array_merge($transaction->resource_data, ['method' => $transaction->method])"
                        />
                        <livewire:admin.integration-resource :id="$transaction->resource_id"/>
                        @if ($transaction->integration_type === \App\Models\Integration\PaymentProvider\NeuronPayment::class)
                            @if($transaction->status === \App\Enums\Transaction\TransactionStatusEnum::Pending)
                                <x-shapes.separator/>
                                <div class="flex items-center my-2">
                                    <x-label class="mr-4">Update</x-label>
                                    <x-buttons.button
                                        wire:click="updateTransactionStatus('{{ $transaction->id }}', '{{ \App\Enums\Transaction\TransactionStatusEnum::Paid->value }}')"
                                        class="mx-2"
                                        color="green"
                                    >{{ \App\Enums\Transaction\TransactionStatusEnum::Paid->value }}</x-buttons.button>
                                    <x-buttons.button
                                        wire:click="updateTransactionStatus('{{ $transaction->id }}', '{{ \App\Enums\Transaction\TransactionStatusEnum::Canceled->value }}')"
                                        class="mx-2"
                                        color="gray"
                                    >{{ \App\Enums\Transaction\TransactionStatusEnum::Canceled->value }}</x-buttons.button>
                                </div>
                            @endif
                        @endif
                    </x-blocks.card>
                @endforeach
            @endif
            @if($order->integrations->isNotEmpty())
                @foreach($order->integrations as $integration)
                    <x-blocks.card>
                        <x-compounds.integration-resource
                            :integrationType="$integration->integration_type"
                            :status="$integration->status"
                            :createdAt="$integration->created_at"
                            :updatedAt="$integration->updated_at"
                            :resourceId="$integration->resource_id"
                        />
                    </x-blocks.card>
                @endforeach
            @endif
        </x-grid-3>
    </div>

    {{-- Dev --}}

    <div class="my-4">
        <x-blocks.card x-data="{ toggle: false }">
            <x-label class="block my-2">Payment provider</x-label>
            <x-typography.headline class="mb-8">{{ $paymentProviderName }}</x-typography.headline>
            <div class="flex items-center my-2">
                <x-label class="block mr-2">Mapped payment resource</x-label>
                <x-buttons.button-toggle/>
            </div>
            <div x-show="toggle"
                 x-cloak
            >
                <x-html.pre-json :json="$mappedPaymentResource"/>
            </div>
        </x-blocks.card>
    </div>
    @if(!empty($orderEvents))
        <x-blocks.card x-data="{ toggle: true }"
                       class="my-4"
        >
            <div class="flex items-center">
                <x-label class="block mr-2">Events</x-label>
                <x-buttons.button-toggle/>
            </div>
            <div x-show="toggle"
                 x-cloak
            >
                @foreach(array_reverse($orderEvents) as $orderEvent)
                    <div class="my-8">
                        <x-label class="block">{{ __('actions.' . $orderEvent->action) }}</x-label>
                        <div class="flex items-center my-2">
                            <div class="mr-4">
                                <x-parsing.date-time :value="$orderEvent->created_at"/>
                            </div>
                            <div>
                                <x-parsing.diff-for-humans :value="$orderEvent->created_at"/>
                            </div>
                        </div>
                        <x-html.pre-json :json="json_decode($orderEvent->data)"/>
                    </div>
                @endforeach
            </div>
        </x-blocks.card>
    @endif
</x-admin.layout>
