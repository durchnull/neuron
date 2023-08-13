<?php

namespace App\Http\Resources\Engine\Cart;

use App\Enums\Order\OrderStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'version' => $this->version,
            'number' => $this->status !== OrderStatusEnum::Open ? $this->order_number : null, // @todo?
            'status' => $this->status,
            'amount' => $this->amount,
            'items_amount' => $this->items_amount,
            'items_discount_amount' => $this->items_discount_amount,
            'shipping_amount' => $this->shipping_amount,
            'shipping_discount_amount' => $this->shipping_discount_amount,
            'customer_note' => $this->customer_note,
            'items' => CartItemResource::collection($this->items),
            'coupons' => CartCouponResource::collection($this->coupons),
            'cart_rules' => CartCartRuleResource::collection($this->cartRules),
            'shipping' => CartShippingResource::make($this->whenLoaded('shipping')),
            'payment' => CartPaymentResource::make($this->whenLoaded('payment')),
            'customer' => CartCustomerResource::make($this->whenLoaded('customer')),
            'shipping_address' => CartAddressResource::make($this->whenLoaded('shippingAddress')),
            'billing_address' => CartAddressResource::make($this->whenLoaded('billingAddress')),
            'transactions' => CartTransactionResource::collection($this->whenLoaded('transactions')), // @todo filter active
        ];
    }
}
