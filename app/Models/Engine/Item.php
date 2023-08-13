<?php

namespace App\Models\Engine;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $reference
 * @property int $quantity
 * @property string $order_id
 * @property string $product_id
 * @property int $product_version
 * @property int $total_amount
 * @property int $unit_amount
 * @property int $discount_amount
 * @property int $position
 * @property array $configuration
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Product $product
 */
class Item extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'reference',
        'product_version',
        'total_amount',
        'unit_amount',
        'discount_amount',
        'quantity',
        'position',
        'configuration'
    ];

    protected $casts = [
        'configuration' => 'array'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
