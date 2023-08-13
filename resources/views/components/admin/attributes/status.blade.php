@php
    if (is_bool($value)) {
        $color = $value ? 'green' : 'red';
    } else {
         switch ($value) {
             case 'pending':
             case \App\Enums\Integration\IntegrationResourceStatusEnum::Created:
             case \App\Enums\Order\OrderStatusEnum::Placing:
             case \App\Enums\Transaction\TransactionStatusEnum::Pending:
                $color = 'yellow';
                break;
            case 'active':
            case \App\Enums\Integration\IntegrationResourceStatusEnum::Distributed:
            case \App\Enums\Order\OrderStatusEnum::Accepted:
            case \App\Enums\Order\OrderStatusEnum::Confirmed:
            case \App\Enums\Transaction\TransactionStatusEnum::Authorized:
            case \App\Enums\Transaction\TransactionStatusEnum::Paid:
                $color = 'green';
                break;
            case \App\Enums\Transaction\TransactionStatusEnum::Failed:
            case \App\Enums\Integration\IntegrationResourceStatusEnum::DistributedFailed:
                $color = 'red';
                break;
            case \App\Enums\Order\OrderStatusEnum::Shipped:
                $color = 'blue';
                break;
            case \App\Enums\Order\OrderStatusEnum::Refunded:
            case \App\Enums\Transaction\TransactionStatusEnum::Refunded:
                $color = 'purple';
                break;
            case \App\Enums\Order\OrderStatusEnum::Canceled:
            case \App\Enums\Transaction\TransactionStatusEnum::Canceled:
                $color = 'orange';
                break;
            default:
            case \App\Enums\Order\OrderStatusEnum::Open:
            case \App\Enums\Transaction\TransactionStatusEnum::Created:
                $color = 'gray';
                break;
        }
    }
@endphp
@props(['value' => null])
@if (is_bool($value))
    <div {{ $attributes->merge(['class' => 'flex items-center p-2']) }}>
        <x-shapes.dot class="bg-{{ $color }}-400"/>
    </div>
@else
    <span {{ $attributes->merge(['class' => "inline-flex items-center px-3 py-1 font-bold text-xs border border-". $color."-300 rounded-full bg-". $color."-50 text-". $color."-600"]) }}>
        <x-shapes.dot class="bg-{{ $color }}-400 mr-2"/>
        <span>{{ $value }}</span>
    </span>
@endif
