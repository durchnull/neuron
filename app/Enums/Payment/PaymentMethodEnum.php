<?php

namespace App\Enums\Payment;

enum PaymentMethodEnum: string
{
    case Applepay = 'applepay';
    case Creditcard = 'creditcard';
    case Free = 'free';
    case Giropay = 'giropay';
    case KlarnaPayLater = 'klarnapaylater';
    case Paypal = 'paypal';
    case Prepayment = 'prepayment';
    case Proxy = 'proxy'; // amazon
    case Sofort = 'sofort';

    /**
     * Mollie payment methods
     *
     * @link https://docs.mollie.com/reference/v2/orders-api/create-order
     *
     * applepay
     * bancontact
     * banktransfer
     * belfius
     * billie
     * creditcard
     * directdebit
     * eps
     * giftcard
     * giropay
     * ideal
     * in3
     * kbc
     * klarnapaylater
     * klarnapaynow
     * klarnasliceit
     * mybank
     * paypal
     * paysafecard
     * przelewy24
     * sofort
     * voucher
     */
}
