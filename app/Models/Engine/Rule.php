<?php

namespace App\Models\Engine;

use App\Consequence\ConsequenceCollection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $sales_channel_id
 * @property string $condition_id
 * @property string $name
 * @property ConsequenceCollection $consequences
 * @property int $position
 * @property SalesChannel $salesChannel
 * @property Condition $condition
 */
class Rule extends Model
{
    use HasFactory;
    use HasUuids;

    // @todo [query]
    protected $with = [
        'condition'
    ];

    protected $fillable = [
        'sales_channel_id',
        'condition_id',
        'name',
        'consequences',
        'position',
    ];

    protected $casts = [
        'consequences' => \App\Casts\ConsequenceCollection::class,
    ];

    public function salesChannel(): BelongsTo
    {
        return $this->belongsTo(SalesChannel::class);
    }

    public function condition(): BelongsTo
    {
        return $this->belongsTo(Condition::class);
    }
}
