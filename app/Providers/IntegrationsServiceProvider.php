<?php

namespace App\Providers;

use App\Contracts\Integration\Inventory\BillbeeServiceContract;
use App\Contracts\Integration\Inventory\NeuronInventoryServiceContract;
use App\Contracts\Integration\Inventory\WeclappServiceContract;
use App\Contracts\Integration\Mail\MailgunServiceContract;
use App\Contracts\Integration\Marketing\KlicktippServiceContract;
use App\Contracts\Integration\PaymentProvider\AmazonPayServiceContract;
use App\Contracts\Integration\PaymentProvider\MollieServiceContract;
use App\Contracts\Integration\PaymentProvider\NeuronPaymentServiceContract;
use App\Contracts\Integration\PaymentProvider\PaypalServiceContract;
use App\Contracts\Integration\PaymentProvider\PostFinanceServiceContract;
use App\Enums\Integration\IntegrationTypeEnum;
use App\Models\Integration\Inventory\Billbee;
use App\Models\Integration\Inventory\NeuronInventory;
use App\Models\Integration\Inventory\Weclapp;
use App\Models\Integration\Mail\Mailgun;
use App\Models\Integration\Marketing\Klicktipp;
use App\Models\Integration\PaymentProvider\AmazonPay;
use App\Models\Integration\PaymentProvider\Mollie;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use App\Models\Integration\PaymentProvider\Paypal;
use App\Models\Integration\PaymentProvider\PostFinance;
use App\Services\Engine\IntegrationsService;
use App\Services\Integration\Inventory\BillbeeService;
use App\Services\Integration\Inventory\NeuronInventoryService;
use App\Services\Integration\Inventory\WeclappService;
use App\Services\Integration\Mail\MailgunService;
use App\Services\Integration\PaymentProvider\AmazonPayService;
use App\Services\Integration\PaymentProvider\MollieService;
use App\Services\Integration\PaymentProvider\NeuronPaymentService;
use App\Services\Integration\PaymentProvider\PaypalService;
use App\Services\Integration\PaymentProvider\PostFinanceService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class IntegrationsServiceProvider extends ServiceProvider
{
    protected static array $integrations = [
        IntegrationTypeEnum::PaymentProvider->value => [
            AmazonPay::class => AmazonPayServiceContract::class,
            Mollie::class => MollieServiceContract::class,
            Paypal::class => PaypalServiceContract::class,
            NeuronPayment::class => NeuronPaymentServiceContract::class,
            PostFinance::class => PostFinanceServiceContract::class,
        ],
        IntegrationTypeEnum::Inventory->value => [
            NeuronInventory::class => NeuronInventoryServiceContract::class,
            Billbee::class => BillbeeServiceContract::class,
            Weclapp::class => WeclappServiceContract::class,
        ],
        IntegrationTypeEnum::Mail->value => [
            Mailgun::class => MailgunServiceContract::class
        ],
        IntegrationTypeEnum::Marketing->value => [
            Klicktipp::class => KlicktippServiceContract::class
        ]
    ];


    public function register(): void
    {
        $this->app->singleton(BillbeeServiceContract::class, function (Application $app, array $parameters) {
            return new BillbeeService($parameters['billbee']);
        });

        $this->app->singleton(WeclappServiceContract::class, function (Application $app, array $parameters) {
            return new WeclappService($parameters['weclapp']);
        });

        $this->app->bind(NeuronInventoryServiceContract::class, function (Application $app, array $parameters) {
            return new NeuronInventoryService($parameters['neuronInventory']);
        });

        $this->app->bind(NeuronPaymentServiceContract::class, function (Application $app, array $parameters) {
            return new NeuronPaymentService($parameters['neuronPayment']);
        });

        $this->app->bind(PaypalServiceContract::class, function (Application $app, array $parameters) {
            return new PaypalService($parameters['paypal']);
        });

        $this->app->bind(MollieServiceContract::class, function (Application $app, array $parameters) {
            return new MollieService($parameters['mollie']);
        });

        $this->app->bind(AmazonPayServiceContract::class, function (Application $app, array $parameters) {
            return new AmazonPayService($parameters['amazonPay']);
        });

        $this->app->bind(PostFinanceServiceContract::class, function (Application $app, array $parameters) {
            return new PostFinanceService($parameters['postFinance']);
        });

        $this->app->bind(MailgunServiceContract::class, function (Application $app, array $parameters) {
            return new MailgunService($parameters['mailgun']);
        });

        $this->app->singleton('integrations', function (Application $app) {
            return new IntegrationsService(
                $app->make('sales-channel'),
                static::$integrations
            );
        });
    }

    public function boot(): void
    {
    }
}
