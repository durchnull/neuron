<?php

namespace App\Services\Engine;

use App\Contracts\Engine\SalesChannelContract;
use App\Contracts\Engine\VatServiceContract;
use App\Models\Engine\Vat;

class VatService implements VatServiceContract
{
    public function __construct(protected SalesChannelContract $salesChannelService)
    {
    }

    public function get(string $vatableType, string $vatableId, string $countryCode): ?Vat
    {
        return Vat::where([
            'sales_channel_id' => $this->salesChannelService->id(),
            'vatable_type' => $vatableType,
            'vatable_id' => $vatableId,
            'country_code' => $countryCode,
        ])->first();
    }
}
