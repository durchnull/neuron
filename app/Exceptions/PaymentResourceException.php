<?php

namespace App\Exceptions;

use Exception;

class PaymentResourceException extends Exception
{
    protected $message = 'Payment resource exception';
}
