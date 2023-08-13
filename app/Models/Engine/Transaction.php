<?php

namespace App\Models\Engine;

use App\Enums\Transaction\TransactionStatusEnum;
use App\Models\Integration\Integration;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $id
 * @property string $sales_channel_id
 * @property string $integration_id
 * @property string $integration_type
 * @property string $order_id
 * @property TransactionStatusEnum $status
 * @property string $method
 * @property string $resource_id
 * @property array $resource_data
 * @property string $checkout_url
 * @property SalesChannel $salesChannel
 * @property Order $order
 * @property Integration $integration
 */
class Transaction extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'sales_channel_id',
        'integration_id',
        'integration_type',
        'order_id',
        'status',
        'method',
        'resource_id',
        'resource_data',
        'webhook_id',
        'checkout_url',
    ];

    protected $casts = [
        'status' => TransactionStatusEnum::class,
        'resource_data' => 'array'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function salesChannel(): BelongsTo
    {
        return $this->belongsTo(SalesChannel::class);
    }

    public function integration(): MorphTo
    {
        return $this->morphTo();
    }
}
