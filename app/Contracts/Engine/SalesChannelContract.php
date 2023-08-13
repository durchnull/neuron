<?php

namespace App\Contracts\Engine;

use App\Models\Engine\SalesChannel;

interface SalesChannelContract
{
    public function id(): string;

    public function get(): SalesChannel;

    public function set(SalesChannel $salesChannel): SalesChannelContract;

    public function setByToken(string $token): SalesChannelContract;

    public function setByCartToken(string $token): SalesChannelContract;

    public function removeItemsOnPriceIncrease(): bool;

    public function getCurrencyCode(): string;

}
