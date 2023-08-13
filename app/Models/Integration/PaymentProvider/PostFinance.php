<?php

namespace App\Models\Integration\PaymentProvider;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $sales_channel_id
 * @property bool $enabled
 * @property string $name
 * @property string $space_id
 * @property string $user_id
 * @property string $secret
 */
class PostFinance extends PaymentProvider
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'sales_channel_id',
        'enabled',
        'name',
        'space_id',
        'user_id',
        'secret',
    ];
}
