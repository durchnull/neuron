<?php

namespace App\Models\Integration\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $sales_channel_id
 * @property bool $enabled
 * @property bool $distribute_order
 * @property string $name
 * @property string $user_name
 * @property string $developer_key
 * @property string $customer_key
 * @property string $service
 * @property string $tag_prefix
 * @property array $tags
 * @property array $tags_coupons
 * @property array $tags_periods
 * @property array $tags_new_customer
 * @property array $tags_products
 */
class Klicktipp extends Marketing
{
    use HasFactory;

    protected $fillable = [
        'sales_channel_id',
        'enabled',
        'distribute_order',
        'name',
        'user_name',
        'developer_key',
        'customer_key',
        'service',
        'tag_prefix',
        'tags',
        'tags_coupons',
        'tags_periods',
        'tags_new_customer',
        'tags_products',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'tags' => 'array',
        'tags_coupons' => 'array',
        'tags_periods' => 'array',
        'tags_new_customer' => 'array',
        'tags_products' => 'array',
    ];
}
