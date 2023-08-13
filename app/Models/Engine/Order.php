<?php

namespace App\Models\Engine;

use App\Enums\Order\OrderStatusEnum;
use App\Models\Integration\OrderIntegration;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property string $id
 * @property int $version
 * @property string $sales_channel_id
 * @property string $shipping_id
 * @property string $payment_id
 * @property string $customer_id
 * @property string $transaction_id
 * @property string $shipping_address_id
 * @property string $billing_address_id
 * @property string $order_number
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $ordered_at
 * @property OrderStatusEnum $status
 * @property \Illuminate\Database\Eloquent\Collection|Item[] $items
 * @property \Illuminate\Database\Eloquent\Collection|Transaction[] $transactions
 * @property \Illuminate\Database\Eloquent\Collection|Coupon[] $coupons
 * @property \Illuminate\Database\Eloquent\Collection|CartRule[] $cartRules
 * @property Shipping|null $shipping
 * @property Payment|null $checkout
 * @property Customer|null $customer
 * @property int $amount
 * @property int $items_amount
 * @property int $items_discount_amount
 * @property int $shipping_amount
 * @property int $shipping_discount_amount
 * @property string $customer_note
 * @property Address $billingAddress
 * @property Address $shippingAddress
 * @property SalesChannel $salesChannel
 * @property Payment $payment
 *
 */
class Order extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'sales_channel_id',
        'status',
        'version',
        'shipping_id',
        'payment_id',
        'customer_id',
        'billing_address_id',
        'shipping_address_id',
        'amount',
        'items_amount',
        'items_discount_amount',
        'shipping_amount',
        'shipping_discount_amount',
        'ordered_at',
        'order_number',
        'customer_note',
    ];

    protected $attributes = [
        'version' => 1,
        'amount' => 0,
        'items_amount' => 0,
        'items_discount_amount' => 0,
        'shipping_amount' => 0,
        'shipping_discount_amount' => 0,
        'status' => OrderStatusEnum::Open,
    ];

    protected $casts = [
        'status' => OrderStatusEnum::class,
        'ordered_at' => 'datetime'
    ];

    public function salesChannel(): BelongsTo
    {
        return $this->belongsTo(SalesChannel::class);
    }

    /**
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class)
            ->orderBy('position');
    }

    /**
     * @return BelongsToMany
     */
    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class)
            ->withPivot('created_at')
            ->orderByPivot('created_at', 'asc');
    }

    /**
     * @return BelongsToMany
     */
    public function cartRules(): BelongsToMany
    {
        return $this->belongsToMany(CartRule::class)
            ->withPivot('created_at')
            ->orderByPivot('created_at', 'asc');
    }

    public function rules(): HasManyThrough
    {
        return $this->hasManyThrough(Rule::class, Coupon::class);
    }

    /**
     * @return BelongsTo
     */
    public function shipping(): BelongsTo
    {
        return $this->belongsTo(Shipping::class);
    }

    /**
     * @return BelongsTo
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return BelongsTo
     */
    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    /**
     * @return BelongsTo
     */
    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    /**
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * @return HasMany
     */
    public function integrations(): HasMany
    {
        return $this->hasMany(OrderIntegration::class);
    }

    public function billingAddressIsShippingAddress(): bool
    {
        return $this->billing_address_id === $this->shipping_address_id;
    }
}
