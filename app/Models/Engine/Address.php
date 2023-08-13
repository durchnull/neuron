<?php

namespace App\Models\Engine;

use App\Enums\Address\SalutationEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $sales_channel_id
 * @property string $customer_id
 * @property string $company
 * @property string $salutation
 * @property string $first_name
 * @property string $last_name
 * @property string $street
 * @property string $number
 * @property string $additional
 * @property string $postal_code
 * @property string $city
 * @property string $country_code
 * @property Customer $customer
 * @property SalesChannel $salesChannel
 */
class Address extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'sales_channel_id',
        'customer_id',
        'company',
        'salutation',
        'first_name',
        'last_name',
        'street',
        'number',
        'additional',
        'postal_code',
        'city',
        'country_code',
    ];

    protected $casts = [
        'salutation' => SalutationEnum::class
    ];

    public function salesChannel(): BelongsTo
    {
        return $this->belongsTo(SalesChannel::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function label(): string
    {
        return implode(', ', [
            implode(' ', [
                $this->street,
                $this->number,
            ]),
            $this->postal_code,
            $this->city,
            $this->country_code,
        ]);
    }
}
