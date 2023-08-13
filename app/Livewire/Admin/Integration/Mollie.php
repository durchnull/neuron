<?php

namespace App\Livewire\Admin\Integration;

use App\Contracts\Integration\PaymentProvider\MollieServiceContract;
use App\Facades\SalesChannel;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Livewire\Component;

class Mollie extends Component
{
    public string $id;

    public string $headline;

    public string $name;

    public ?bool $integrationTest;

    public bool $enabled;

    public string $apiKey;

    public string $profileId;

    public function mount(string $id)
    {
        $this->id = $id;
        /** @var \App\Models\Integration\PaymentProvider\Mollie $mollie */
        $mollie = \App\Models\Integration\PaymentProvider\Mollie::find($id);

        $this->headline = $mollie->name;
        $this->name = $mollie->name;
        $this->enabled = $mollie->enabled;
        $this->apiKey = $mollie->api_key;
        $this->profileId = $mollie->profile_id;
    }


    public function testIntegration()
    {
        /** @var \App\Models\Integration\PaymentProvider\Mollie $model */
        $model = \App\Models\Integration\PaymentProvider\Mollie::with('salesChannel')->find($this->id);
        SalesChannel::set($model->salesChannel);
        $service = App::make(MollieServiceContract::class, [Str::camel(class_basename(\App\Models\Integration\PaymentProvider\Mollie::class)) => $model]);
        $this->integrationTest = $service->test();;
    }


    public function render()
    {
        return view('livewire.admin.integration.mollie');
    }
}
