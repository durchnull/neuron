<?php

namespace App\Services\Engine;

use App\Contracts\Engine\SalesChannelContract;
use App\Models\Engine\SalesChannel;
use Illuminate\Support\Facades\Session;

class SalesChannelService implements SalesChannelContract
{
    protected ?SalesChannel $salesChannel;

    public function __construct()
    {
        $this->salesChannel = null;
    }

    public function id(): string
    {
        return $this->salesChannel->id;
    }

    public function get(): SalesChannel
    {
        return $this->salesChannel;
    }

    public function set(SalesChannel $salesChannel): SalesChannelContract
    {
        $this->salesChannel = $salesChannel;

        Session::put('admin.sales_channel_id', $salesChannel->id);
        Session::put('admin.sales_channel_token', $salesChannel->token);
        Session::put('admin.sales_channel_cart_token', $salesChannel->cart_token);

        return $this;
    }

    public function setByToken(string $token): SalesChannelContract
    {
        $salesChannel = SalesChannel::where([
            'token' => $token
        ])->first();

        $this->set($salesChannel);

        return $this;
    }

    public function setByCartToken(string $token): SalesChannelContract
    {
        $salesChannel = SalesChannel::where([
            'cart_token' => $token
        ])->first();

        $this->set($salesChannel);

        return $this;
    }

    public function removeItemsOnPriceIncrease(): bool
    {
        return $this->salesChannel->remove_items_on_price_increase;
    }

    public function getCurrencyCode(): string
    {
        return $this->salesChannel->currency_code;
    }
}
