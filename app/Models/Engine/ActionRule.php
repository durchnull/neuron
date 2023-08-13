<?php

namespace App\Models\Engine;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $sales_channel_id
 * @property string $condition_id
 * @property string $action
 * @property string $name
 * @property bool $enabled
 * @property SalesChannel $salesChannel
 * @property Condition $condition
 */
class ActionRule extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'sales_channel_id',
        'condition_id',
        'name',
        'action',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean'
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
