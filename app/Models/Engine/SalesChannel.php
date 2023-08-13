<?php

namespace App\Models\Engine;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $merchant_id
 * @property string $name
 * @property string $token
 * @property string $cart_token
 * @property string $currency_code
 * @property array $domains
 * @property string $locale
 * @property bool $use_stock
 * @property bool $remove_items_on_price_increase
 * @property string $checkout_summary_url
 * @property string $order_summary_url
 * @property Merchant $merchant
 */
class SalesChannel extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'merchant_id',
        'name',
        'currency_code',
        'locale',
        'token',
        'cart_token',
        'domains',
        'use_stock',
        'remove_items_on_price_increase',
        'checkout_summary_url',
        'order_summary_url',
    ];

    protected $casts = [
        'use_stock' => 'boolean',
        'remove_items_on_price_increase' => 'boolean',
        'domains' => 'array'
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
