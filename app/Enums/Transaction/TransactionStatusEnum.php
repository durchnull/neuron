<?php

namespace App\Enums\Transaction;

enum TransactionStatusEnum: string
{
    case Created = 'created';
    case Pending = 'pending';
    case Authorized = 'authorized';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';
    case Canceled = 'canceled';
}
