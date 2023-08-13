<?php

namespace App\Livewire\Admin\Integration;

use App\Contracts\Integration\PaymentProvider\PaypalServiceContract;
use App\Facades\SalesChannel;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Livewire\Component;

class Paypal extends Component
{
    public string $id;

    public string $headline;

    public string $name;

    public ?bool $integrationTest;

    public bool $enabled;

    public string $clientId;

    public string $clientSecret;

    public string $accessToken;

    public ?Carbon $accessTokenExpiresAt;

    public function mount(string $id)
    {
        $this->id = $id;
        /** @var \App\Models\Integration\PaymentProvider\Paypal $paypal */
        $paypal = \App\Models\Integration\PaymentProvider\Paypal::find($id);

        $this->headline = $paypal->name;
        $this->name = $paypal->name;
        $this->enabled = $paypal->enabled;
        $this->clientId = $paypal->client_id;
        $this->clientSecret = $paypal->client_secret;
        $this->accessToken = $paypal->access_token ?? '';
        $this->accessTokenExpiresAt = $paypal->access_token_expires_at;
    }

    public function refreshAccessToken()
    {
        /** @var \App\Models\Integration\PaymentProvider\Paypal $model */
        $model = \App\Models\Integration\PaymentProvider\Paypal::with('salesChannel')->find($this->id);
        SalesChannel::set($model->salesChannel);
        $service = App::make(PaypalServiceContract::class, [Str::camel(class_basename(\App\Models\Integration\PaymentProvider\Paypal::class)) => $model]);
        $service->refreshAccessToken();
        $this->mount($this->id);
    }

    public function testIntegration()
    {
        /** @var \App\Models\Integration\PaymentProvider\Paypal $model */
        $model = \App\Models\Integration\PaymentProvider\Paypal::with('salesChannel')->find($this->id);
        SalesChannel::set($model->salesChannel);
        $service = App::make(PaypalServiceContract::class, [Str::camel(class_basename(\App\Models\Integration\PaymentProvider\Paypal::class)) => $model]);
        $this->integrationTest = $service->test();;
    }


    public function render()
    {
        return view('livewire.admin.integration.paypal');
    }
}
