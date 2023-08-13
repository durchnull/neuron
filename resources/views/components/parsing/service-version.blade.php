@props([
    'service' => $service
])
@if(\Illuminate\Support\Facades\App::environment('local'))
    @php
        $clientVersion = '';

        switch (\Illuminate\Support\Str::slug($service)) {
            case 'amazon-pay':
                $clientVersion = \App\Services\Integration\PaymentProvider\AmazonPayService::getClientVersion();
                break;
            case 'mollie':
                $clientVersion = \App\Services\Integration\PaymentProvider\MollieService::getClientVersion();
                break;
            case 'neuron-payment':
                $clientVersion = \App\Services\Integration\PaymentProvider\NeuronPaymentService::getClientVersion();
                break;
            case 'neuron-inventory':
                $clientVersion = \App\Services\Integration\Inventory\NeuronInventoryService::getClientVersion();
                break;
            case 'billbee':
                $clientVersion = \App\Services\Integration\Inventory\BillbeeService::getClientVersion();
                break;
            case 'weclapp':
                $clientVersion = \App\Services\Integration\Inventory\WeclappService::getClientVersion();
                break;
            case 'mailgun':
                $clientVersion = \App\Services\Integration\Mail\MailgunService::getClientVersion();
                break;
            case 'klicktipp':
                $clientVersion = \App\Services\Integration\Marketing\KlicktippService::getClientVersion();
                break;
        }
    @endphp
    <x-shapes.pill>{{ $clientVersion }}</x-shapes.pill>
@endif
