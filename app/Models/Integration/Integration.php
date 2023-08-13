<?php

namespace App\Models\Integration;

use App\Enums\Integration\IntegrationTypeEnum;
use App\Models\Engine\SalesChannel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $name
 * @property bool $enabled
 * @property string $sales_channel_id
 * @property SalesChannel $salesChannel
 */
abstract class Integration extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'sales_channel_id',
        'enabled',
        'name',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function getIntegrationProvider(): string
    {
        return class_basename($this);
    }

    abstract public function getIntegrationType(): IntegrationTypeEnum;

    public function getIntegrationProviderAttribute(): string
    {
        return $this->getIntegrationProvider();
    }

    public function getIntegrationTypeAttribute(): IntegrationTypeEnum
    {
        return $this->getIntegrationType();
    }

    public function getTable(): string
    {
        return 'integration_' . Str::snake(class_basename($this));
    }

    public function salesChannel(): BelongsTo
    {
        return $this->belongsTo(SalesChannel::class);
    }
}
