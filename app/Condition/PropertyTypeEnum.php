<?php

namespace App\Condition;

enum PropertyTypeEnum: string
{
    case ActionProductId = 'action_product_id';

    case CustomerIsNew = 'customer_is_new';

    case CustomerEmail = 'customer_email';

    case Date = 'date';
    case DateTime = 'datetime';

    case OrderItemsTotalAmount = 'order_items_total_amount';
    case OrderProductIds = 'order_product_ids';
    case OrderProductQuantity = 'order_product_quantity';

    case ShippingCountryCode = 'shipping_country_code';
}
