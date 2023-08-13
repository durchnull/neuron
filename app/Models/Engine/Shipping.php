<?php

namespace App\Models\Engine;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property string $sales_channel_id
 * @property string $id
 * @property bool $enabled
 * @property string $name
 * @property string $country_code
 * @property int $net_price
 * @property int $gross_price
 * @property string $currency_code
 * @property int $position
 * @property bool $default
 * @property Collection $vats
 */
class Shipping extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'sales_channel_id',
        'enabled',
        'name',
        'country_code',
        'net_price',
        'gross_price',
        'currency_code',
        'position',
        'default',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'default' => 'boolean'
    ];

    public function salesChannel(): BelongsTo
    {
        return $this->belongsTo(SalesChannel::class);
    }

    public function vats(): MorphMany
    {
        return $this->morphMany(Vat::class, 'vatable');
    }
}
