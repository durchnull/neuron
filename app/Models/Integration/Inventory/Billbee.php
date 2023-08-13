<?php

namespace App\Models\Integration\Inventory;

use App\Models\Engine\SalesChannel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $name
 * @property string $sales_channel_id
 * @property string $enabled
 * @property string $receive_inventory
 * @property string $distribute_order
 * @property string $user
 * @property string $api_password
 * @property string $api_key
 * @property string $shop_id
 */
class Billbee extends Inventory
{
    use HasFactory;
    use HasUuids;

    protected $table = 'integration_billbee';

    protected $fillable = [
        'sales_channel_id',
        'enabled',
        'receive_inventory',
        'distribute_order',
        'name',
        'user',
        'api_password',
        'api_key',
        'shop_id',
    ];

    public function getName(): string
    {
        return $this->name;
    }

    public function salesChannel(): BelongsTo
    {
        return $this->belongsTo(SalesChannel::class);
    }
}
