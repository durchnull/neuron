<?php

namespace App\Models\Engine;

use App\Models\Engine\Order;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $sales_channel_id
 * @property string $email
 * @property string $full_name
 * @property string $phone
 * @property bool $new
 */
class Customer extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'sales_channel_id',
        'email',
        'full_name',
        'phone',
        'order_count',
        'new'
    ];

    protected $casts = [
        'new' => 'boolean'
    ];

    protected $attributes = [
        'new' => true,
        'order_count' => 0
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function primaryShippingAddress(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
