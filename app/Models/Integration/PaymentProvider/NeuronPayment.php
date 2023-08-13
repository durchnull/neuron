<?php

namespace App\Models\Integration\PaymentProvider;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $id
 */
class NeuronPayment extends PaymentProvider
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'sales_channel_id',
        'enabled',
        'name',
    ];
}
