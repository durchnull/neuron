<?php

namespace App\Contracts\Engine;

use App\Models\Engine\Vat;

interface VatServiceContract
{
    public function get(string $vatableType, string $vatableId, string $countryCode): ?Vat;
}
