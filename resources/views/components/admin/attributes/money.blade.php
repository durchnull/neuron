@isset($value)
    <span class="inline-block whitespace-nowrap text-right font-mono font-medium">{{ number_format((int)$value / 100.0, 2, ',', '.') }} {{ \App\Facades\SalesChannel::getCurrencyCode() }}</span>
@endisset
