@php
    // @todo remove and check
    \App\Facades\SalesChannel::setByToken(\Illuminate\Support\Facades\Session::get('admin.sales_channel_token'));
@endphp
<span {{ $attributes->merge(['class' => 'font-mono whitespace-nowrap']) }}>{{ number_format($amount / 100.0, 2, ',', '.') }} {{ \App\Facades\SalesChannel::getCurrencyCode() }}</span>
