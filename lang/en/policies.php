<?php

return [
    'quantity-limit' => [
        'description' => 'The product\'s quantity limit is reached',
        'suggestion' => ''
    ],
    'out-of-stock' => [
        'description' => 'The product is out of stock',
        'suggestion' => ''
    ],
    'cart-is-empty' => [
        'description' => 'The cart is empty',
        'suggestion' => ''
    ],
    'customer-not-set' => [
        'description' => 'The customer is not set',
        'suggestion' => ''
    ],
    'order-flow-constraint' => [
        'description' => 'The order action is not possible in the current state',
        'suggestion' => ''
    ],
    'coupon-is-redeemed' => [
        'description' => 'The coupon already is redeemed',
        'suggestion' => ''
    ],
    'coupon-is-not-combinable' => [
        'description' => 'The coupon is not combinable',
        'suggestion' => ''
    ],
    'incomplete-transactions' => [
        'description' => 'The cart has incomplete transactions',
        'suggestion' => '',
    ],
    'item-locked' => [
        'description' => 'The cart item is locked',
        'suggestion' => '',
    ],
    'payment-is-free' => [
        'description' => 'The payment is free', // @todo [payment] should be an exception
        'suggestion' => '',
    ],
    'payment-is-not-free' => [
        'description' => 'The payment is not free', // @todo [payment] should be an exception
        'suggestion' => '',
    ],
    'action-rule' => [
        'description' => 'The action is restricted by a custom rule',
        'suggestion' => '',
    ],
    'model-not-found' => [
        'description' => 'The model was not found',
        'suggestion' => '',
    ],
];
