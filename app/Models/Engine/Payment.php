<?php

namespace App\Models\Engine;

use App\Enums\Payment\PaymentMethodEnum;
use App\Models\Integration\Integration;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $sales_channel_id
 * @property string $integration_id
 * @property string $integration_type
 * @property bool $enabled
 * @property Integration $integration
 * @property string $name
 * @property PaymentMethodEnum $method
 * @property int $position
 * @property string $description
 * @property string $id
 * @property bool $default
 */
class Payment extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'sales_channel_id',
        'integration_id',
        'integration_type',
        'enabled',
        'name',
        'method',
        'position',
        'description',
        'default',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'default' => 'boolean',
        'method' => PaymentMethodEnum::class,
    ];

    public function salesChannel(): BelongsTo
    {
        return $this->belongsTo(SalesChannel::class);
    }

    public function integration(): MorphTo
    {
        return $this->morphTo();
    }
}
