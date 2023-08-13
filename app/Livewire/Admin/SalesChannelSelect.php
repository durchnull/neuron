<?php

namespace App\Livewire\Admin;

use App\Models\Engine\SalesChannel;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class SalesChannelSelect extends Component
{
    public ?string $salesChannelId;

    public array $options;

    public function mount()
    {
        $salesChannel = \App\Facades\SalesChannel::get();
        $this->salesChannelId = $salesChannel->id;

        $this->options = SalesChannel::where([
                'merchant_id' => $salesChannel->merchant_id
            ])
            ->get()
            ->map(fn(SalesChannel $salesChannel) => [
                'value' => $salesChannel->id,
                'label' => $salesChannel->name,
                'selected' => $this->salesChannelId === $salesChannel->id
            ])->toArray();
    }

    public function updated($name, $value)
    {
        $salesChannel = SalesChannel::find($value);

        \App\Facades\SalesChannel::set($salesChannel);

        $this->redirectRoute('admin.home');
    }

    public function render()
    {
        return view('livewire.admin.sales-channel-select');
    }
}
