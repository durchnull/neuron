<?php

namespace App\Models\Engine;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $product_id
 * @property int $net_price
 * @property int $gross_price
 * @property bool $enabled
 * @property Carbon $begin_at
 * @property Carbon $end_at
 * @property Product $product
 */
class ProductPrice extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'product_id',
        'net_price',
        'gross_price',
        'begin_at',
        'end_at',
        'enabled',
    ];

    protected $casts = [
        'begin_at' => 'datetime',
        'end_at' => 'datetime',
        'enabled' => 'boolean',
    ];

    public function scopeActive(Builder $query): void
    {
        $query->where('enabled', true)
            ->where('begin_at', '<=', now())
            ->where('end_at', '>', now());
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isActive(): bool
    {
        $now = now();

        return $this->enabled === true &&
            $this->begin_at <= $now &&
            $this->end_at > $now;
    }
}
