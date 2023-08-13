<?php

namespace App\Models\Integration\PaymentProvider;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $sales_channel_id
 * @property bool $enabled
 * @property string $name
 * @property string $client_id
 * @property string $client_secret
 * @property string $access_token
 * @property Carbon $access_token_expires_at
 */
class Paypal extends PaymentProvider
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'sales_channel_id',
        'enabled',
        'name',
        'client_id',
        'client_secret',
        'access_token',
        'access_token_expires_at',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'access_token_expires_at' => 'datetime'
    ];
}
