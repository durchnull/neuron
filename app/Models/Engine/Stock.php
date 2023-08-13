<?php

namespace App\Models\Engine;

use App\Models\Engine\Product;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stock';

    protected $fillable = [
        'product_id',
        'value',
        'queue',
    ];

    public function updateTimestamps()
    {
        $time = $this->freshTimestamp();

        $updatedAtColumn = $this->getUpdatedAtColumn();

        if (!$this->exists && !is_null($updatedAtColumn) && !$this->isDirty($updatedAtColumn)) {
            $this->setUpdatedAt($time);
        }

        return $this;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
