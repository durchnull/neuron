<?php

namespace App\Models\Integration;

use App\Enums\Integration\IntegrationResourceStatusEnum;
use App\Models\Engine\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $id
 * @property string $order_id
 * @property string $integration_id
 * @property string $integration_type
 * @property string $resource_id
 * @property IntegrationResourceStatusEnum $status
 */
class OrderIntegration extends Model
{
    use HasFactory;

    protected $table = 'order_integration';

    protected $fillable = [
        'order_id',
        'integration_id',
        'integration_type',
        'resource_id',
        'status',
    ];

    protected $casts = [
        'status' => IntegrationResourceStatusEnum::class
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function integration(): MorphTo
    {
        return $this->morphTo();
    }
}
