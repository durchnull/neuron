<?php

namespace App\Livewire\Admin\Integration;

use App\Contracts\Integration\Inventory\WeclappServiceContract;
use App\Facades\SalesChannel;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Weclapp extends Component
{
    public string $headline;

    public bool $syncingInventory;

    #[Locked]
    public string $id;

    #[Rule('required|min:3')]
    public string $name;

    #[Rule('required|bool')]
    public bool $enabled;

    #[Rule('required|bool')]
    public bool $distributeOrder;

    #[Rule('required|bool')]
    public bool $receiveInventory;

    #[Rule('required|string')]
    public ?string $distributionChannel;

    #[Rule('required|string')]
    public ?string $articleCategoryId;

    #[Rule('required|string')]
    public string $url;

    #[Rule('required|string')]
    public string $apiToken;

    public ?bool $integrationTest;

    public function mount(string $id)
    {
        /** @var \App\Models\Integration\Inventory\Weclapp $weclapp */
        $weclapp = \App\Models\Integration\Inventory\Weclapp::find($id);

        $this->headline = $weclapp->name;
        $this->id = $weclapp->id;

        $this->name = $weclapp->name;
        $this->enabled = $weclapp->enabled;
        $this->distributeOrder = $weclapp->distribute_order;
        $this->receiveInventory = $weclapp->receive_inventory;
        $this->url = $weclapp->url;
        $this->apiToken = $weclapp->api_token;
        $this->distributionChannel = $weclapp->distribution_channel;
        $this->articleCategoryId = $weclapp->article_category_id;
    }

    public function syncInventory()
    {
        $this->syncingInventory = true;

        try {
            /** @var \App\Models\Integration\Inventory\Weclapp $model */
            $model = \App\Models\Integration\Inventory\Weclapp::with('salesChannel')->find($this->id);
            SalesChannel::set($model->salesChannel);
            $service = App::make(WeclappServiceContract::class, [Str::camel(class_basename(\App\Models\Integration\Inventory\Weclapp::class)) => $model]);
            $service->receiveInventory();
        } catch (\Exception $exception) {
        }

        $this->syncingInventory = false;
    }

    public function testIntegration()
    {
        $model = \App\Models\Integration\Inventory\Weclapp::find($this->id);
        $service = App::make(WeclappServiceContract::class, [Str::camel(class_basename(\App\Models\Integration\Inventory\Weclapp::class)) => $model]);
        $this->integrationTest = $service->test();;
    }

    public function render()
    {
        return view('livewire.admin.integration.weclapp');
    }
}
