<?php

namespace App\Livewire\Admin\Integration;

use App\Contracts\Integration\PaymentProvider\AmazonPayServiceContract;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Livewire\Component;

class AmazonPay extends Component
{
    public string $id;
    public string $headline;

    public bool $enabled;

    public string $name;
    public string $merchantAccountId;
    public string $publicKeyId;
    public string $privateKey;
    public string $region;
    public string $storeId;
    public bool $sandbox;

    public ?bool $integrationTest;

    public function mount(string $id)
    {
        /** @var \App\Models\Integration\PaymentProvider\AmazonPay $amazonPay */
        $amazonPay = \App\Models\Integration\PaymentProvider\AmazonPay::find($id);

        $this->id = $amazonPay->id;
        $this->headline = $amazonPay->name;
        $this->enabled = $amazonPay->enabled;
        $this->name = $amazonPay->name;
        $this->merchantAccountId = $amazonPay->merchant_account_id;
        $this->publicKeyId = $amazonPay->public_key_id;
        $this->privateKey = $amazonPay->private_key;
        $this->region = $amazonPay->region;
        $this->storeId = $amazonPay->store_id;
        $this->sandbox = $amazonPay->sandbox;
    }

    public function testIntegration()
    {
        $model = \App\Models\Integration\PaymentProvider\AmazonPay::find($this->id);
        $service = App::make(AmazonPayServiceContract::class, [Str::camel(class_basename(\App\Models\Integration\PaymentProvider\AmazonPay::class)) => $model]);
        $this->integrationTest = $service->test();;
    }

    public function render()
    {
        return view('livewire.admin.integration.amazon-pay');
    }
}
