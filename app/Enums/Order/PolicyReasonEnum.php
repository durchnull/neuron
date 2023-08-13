<?php

namespace App\Enums\Order;

enum PolicyReasonEnum: string
{
    case QuantityLimit = 'quantity-limit';
    case OutOfStock = 'out-of-stock';
    case CartIsEmpty = 'cart-is-empty';
    case CustomerNotSet = 'customer-not-set';
    case OrderFlowConstraint = 'order-flow-constraint';
    case CouponIsRedeemed = 'coupon-is-redeemed';
    case CouponIsNotCombinable = 'coupon-is-not-combinable';
    case IncompleteTransactions = 'incomplete-transactions';
    case ItemLocked = 'item-locked';
    case PaymentIsFree = 'payment-is-free';

    case PaymentIsNotFree = 'payment-is-not-free';

    case ActionRule = 'action-rule';

    case ModelNotFound = 'model-not-found';

    case ModelIsReferenced = 'model-is-referenced';
}
