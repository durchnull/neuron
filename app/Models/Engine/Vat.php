<?php

namespace App\Models\Engine;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $id
 * @property string $sales_channel_id
 * @property string $vatable_id
 * @property string $vatable_type
 * @property string $country_code
 * @property string $rate
 */
class Vat extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'sales_channel_id',
        'vatable_id',
        'vatable_type',
        'country_code',
        'rate',
    ];

    public function vatable(): MorphTo
    {
        return $this->morphTo();
    }
}
