<?php

namespace App\Models\Integration\PaymentProvider;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $sales_channel_id
 * @property bool $enabled
 * @property string $name
 * @property string $api_key
 * @property string $profile_id
 */
class Mollie extends PaymentProvider
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'sales_channel_id',
        'enabled',
        'name',
        'api_key',
        'profile_id',
    ];

    protected $casts = [
        'enabled' => 'boolean'
    ];
}
