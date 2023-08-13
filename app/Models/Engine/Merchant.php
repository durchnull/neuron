<?php

namespace App\Models\Engine;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $token
 * @property string $name
 * @property Collection $salesChannels
 * @property Collection $users
 */
class Merchant extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'token',
        'name',
    ];

    public function salesChannels(): HasMany
    {
        return $this->hasMany(SalesChannel::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
