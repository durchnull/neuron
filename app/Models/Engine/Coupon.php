<?php

namespace App\Models\Engine;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $sales_channel_id
 * @property string $rule_id
 * @property string $name
 * @property string $code
 * @property bool $enabled
 * @property bool $combinable
 * @property Rule $rule
 */
class Coupon extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'sales_channel_id',
        'rule_id',
        'name',
        'code',
        'enabled',
        'combinable'
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'combinable' => 'boolean',
    ];

    /**
     * Scope a query to only include enabled rules.
     */
    public function scopeEnabled(Builder $query): void
    {
        $query->where('enabled', true);
    }

    /**
     * @return BelongsTo
     */
    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }

    public function salesChannel(): BelongsTo
    {
        return $this->belongsTo(SalesChannel::class);
    }
}
