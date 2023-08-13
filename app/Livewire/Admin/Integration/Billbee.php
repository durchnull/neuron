<?php

namespace App\Livewire\Admin\Integration;

use App\Contracts\Integration\Inventory\BillbeeServiceContract;
use App\Facades\SalesChannel;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Billbee extends Component
{
    public string $headline;

    public bool $syncingInventory;

    public ?bool $integrationTest;

    public string $integrationUrl;

    #[Locked]
    public string $id;

    #[Rule('required|exists:sales_channels,id')]
    public string $salesChannelId;

    #[Rule('required|min:3')]
    public string $name;

    #[Rule('required|email')]
    public string $user;

    #[Rule('required|string')]
    public string $apiPassword;

    #[Rule('required|string')]
    public string $apiKey;

    #[Rule('required|string')]
    public string $shopId;

    #[Rule('required|bool')]
    public bool $enabled;

    #[Rule('required|bool')]
    public bool $distributeOrder;

    #[Rule('required|bool')]
    public bool $receiveInventory;

    public function mount(string $id)
    {
        $billbee = \App\Models\Integration\Inventory\Billbee::find($id);

        $this->syncingInventory = false;
        $this->headline = $billbee->name;
        $this->integrationUrl = route('integration.billbee.entry', [
            'salesChannelId' => $billbee->sales_channel_id,
            'billbeeId' => $billbee->id,
        ]);
        $this->id = $id;
        $this->salesChannelId = $billbee->sales_channel_id;
        $this->name = $billbee->name;
        $this->user = $billbee->user;
        $this->apiPassword = $billbee->api_password;
        $this->apiKey = $billbee->api_key;
        $this->shopId = $billbee->shop_id;
        $this->enabled = $billbee->enabled;
        $this->distributeOrder = $billbee->distribute_order;
        $this->receiveInventory = $billbee->receive_inventory;
    }

    public function syncInventory()
    {
        $this->syncingInventory = true;

        try {
            /** @var \App\Models\Integration\Inventory\Billbee $model */
            $model = \App\Models\Integration\Inventory\Billbee::with('salesChannel')->find($this->id);
            SalesChannel::set($model->salesChannel);
            $service = App::make(BillbeeServiceContract::class, [Str::camel(class_basename(\App\Models\Integration\Inventory\Billbee::class)) => $model]);
            $service->receiveInventory();
        } catch (\Exception $exception) {
        }

        $this->syncingInventory = false;
    }

    public function testIntegration()
    {
        /** @var \App\Models\Integration\Inventory\Billbee $model */
        $model = \App\Models\Integration\Inventory\Billbee::with('salesChannel')->find($this->id);
        SalesChannel::set($model->salesChannel);
        $service = App::make(BillbeeServiceContract::class, [Str::camel(class_basename(\App\Models\Integration\Inventory\Billbee::class)) => $model]);
        $this->integrationTest = $service->test();;
    }

    public function render()
    {
        return view('livewire.admin.integration.billbee');
    }
}
