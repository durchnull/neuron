<?php

namespace App\Models\Engine;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $sales_channel_id
 * @property string $rule_id
 * @property string $name
 * @property bool $enabled
 * @property SalesChannel $salesChannel
 * @property Rule $rule
 */
class CartRule extends Model
{
    use HasFactory;
    use HasUuids;

    protected $with = [
        'rule'
    ];

    protected $fillable = [
        'sales_channel_id',
        'rule_id',
        'name',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean'
    ];

    public function salesChannel(): BelongsTo
    {
        return $this->belongsTo(SalesChannel::class);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }
}
