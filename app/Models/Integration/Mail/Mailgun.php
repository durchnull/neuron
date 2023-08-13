<?php

namespace App\Models\Integration\Mail;

/**
 * @property string $id
 * @property string $sales_channel_id
 * @property bool $enabled
 * @property bool $distribute_order
 * @property bool $refund_order
 * @property string $secret
 * @property string $api_key
 * @property string $endpoint
 * @property string $domain
 * @property string $order_template
 * @property string $refund_template
 * @property string $order_subject
 * @property string $refund_subject
 * @property string $from
 * @property string $sandbox_to
 */
class Mailgun extends Mail
{
    protected $fillable = [
        'sales_channel_id',
        'enabled',
        'distribute_order',
        'refund_order',
        'name',
        'domain',
        'endpoint',
        'secret',
        'api_key',
        'order_template',
        'refund_template',
        'order_subject',
        'refund_subject',
        'from',
        'sandbox_to',
    ];
}
