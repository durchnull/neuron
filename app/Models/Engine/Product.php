<?php

namespace App\Models\Engine;

use App\Enums\Product\ProductTypeEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $id
 * @property string $sales_channel_id
 * @property string $inventoryable_type
 * @property string $inventoryable_id
 * @property string|null $inventory_id
 * @property bool $enabled
 * @property ProductTypeEnum $type
 * @property int $version
 * @property string $sku
 * @property string $ean
 * @property string $name
 * @property int $net_price
 * @property int $gross_price
 * @property Stock $stock
 * @property array $configuration
 * @property string $url
 * @property string $image_url
 * @property Collection $vats
 * @property Collection $prices
 * @property Collection $activePrices
 */
class Product extends Model
{
    use HasFactory;
    use HasUuids;

    protected $with = [
        'activePrices'
    ];

    protected $fillable = [
        'sales_channel_id',
        'inventoryable_type',
        'inventoryable_id',
        'inventory_id',
        'enabled',
        'type',
        'version',
        'sku',
        'ean',
        'name',
        'net_price',
        'gross_price',
        'configuration',
        'url',
        'image_url',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'type' => ProductTypeEnum::class,
        'configuration' => 'array'
    ];

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class);
    }

    public function salesChannel(): BelongsTo
    {
        return $this->belongsTo(SalesChannel::class);
    }

    public function inventoryable(): MorphTo
    {
        return $this->morphTo();
    }

    public function vats(): MorphMany
    {
        return $this->morphMany(Vat::class, 'vatable');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function activePrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class)->active();
    }

    public function getPrice(): int
    {
        return optional($this->activePrices->first())->net_price ?? $this->net_price;
    }
}
