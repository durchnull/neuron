<?php

namespace App\Models\Integration\PaymentProvider;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $id
 * @property string $sales_channel_id
 * @property bool $enabled
 * @property string $name
 * @property string $merchant_account_id
 * @property string $public_key_id
 * @property string $private_key
 * @property string $region
 * @property bool $sandbox
 * @property string $store_id
 *
 */
class AmazonPay extends PaymentProvider
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'sales_channel_id',
        'enabled',
        'name',
        'merchant_account_id',
        'public_key_id',
        'private_key',
        'region',
        'store_id',
        'sandbox',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'sandbox' => 'boolean'
    ];
}
