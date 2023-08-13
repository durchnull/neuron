<?php

namespace App\Models\Integration\Inventory;

use App\Models\Engine\SalesChannel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $sales_channel_id
 * @property string $name
 */
class NeuronInventory extends Inventory
{
    use HasFactory;
    use HasUuids;

    protected $table = 'integration_neuron_inventory';

    protected $fillable = [
        'sales_channel_id',
        'enabled',
        'receive_inventory',
        'distribute_order',
        'name'
    ];

    public function getTable(): string
    {
        return $this->table;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function salesChannel(): BelongsTo
    {
        return $this->belongsTo(SalesChannel::class);
    }
}
