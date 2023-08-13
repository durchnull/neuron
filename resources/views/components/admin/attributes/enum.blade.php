@if ($value instanceof \App\Enums\Payment\PaymentMethodEnum)
    <img src="/storage/payment/{{ $value->value }}.png"
         class="w-14 h-auto"
    >
@else
    <span class="max-w-xs whitespace-nowrap">{{ $value->value }}</span>
@endif
