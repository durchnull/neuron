<?php

namespace App\Models\Engine;

use App\Condition\ConditionCollection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $sales_channel_id
 * @property string $name
 * @property ConditionCollection $collection
 * @property SalesChannel $salesChannel
 */
class Condition extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'sales_channel_id',
        'name',
        'collection',
    ];

    protected $casts = [
        'collection' => \App\Casts\ConditionCollection::class,
    ];

    public function salesChannel(): BelongsTo
    {
        return $this->belongsTo(SalesChannel::class);
    }
}
