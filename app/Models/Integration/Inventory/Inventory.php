<?php

namespace App\Models\Integration\Inventory;

use App\Enums\Integration\IntegrationTypeEnum;
use App\Models\Integration\Integration;
use App\Models\Engine\Product;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property string $id
 */
abstract class Inventory extends Integration
{
    protected $casts = [
        'enabled' => 'boolean',
        'receive_inventory' => 'boolean',
        'distribute_order' => 'boolean'
    ];

    public function getIntegrationType(): IntegrationTypeEnum
    {
        return IntegrationTypeEnum::Inventory;
    }

    public function products(): MorphMany
    {
        return $this->morphMany(Product::class, 'inventoryable');
    }
}
