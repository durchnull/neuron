<?php

namespace App\Models\Integration\Inventory;

use App\Models\Engine\SalesChannel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $sales_channel_id
 * @property bool $enabled
 * @property bool $receive_inventory
 * @property bool $distribute_order
 * @property string $name
 * @property string $url
 * @property string $api_token
 * @property string $article_category_id
 * @property string $distribution_channel
 * @property string $warehouse_id
 */
class Weclapp extends Inventory
{
    use HasFactory;
    use HasUuids;

    protected $table = 'integration_weclapp';

    protected $fillable = [
        'sales_channel_id',
        'enabled',
        'receive_inventory',
        'distribute_order',
        'name',
        'url',
        'api_token',
        'article_category_id',
        'distribution_channel',
        'warehouse_id',
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
