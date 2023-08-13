<?php

namespace App\Http\Resources\Engine\Cart;

use App\Enums\Payment\PaymentMethodEnum;
use App\Facades\SalesChannel;
use App\Models\Engine\Payment;
use App\Models\Engine\Product;
use App\Models\Engine\Shipping;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class CartOptions extends JsonResponse
{
    public static function make(): self
    {
        return new self(static::getOptions());
    }

    public static function getOptions(): array
    {
        // @todo [test]
        return [
            'data' => [
                'payments' => CartPaymentOptionResource::collection(static::getPayments()),
                'products' => CartProductOptionResource::collection(static::getProducts()),
                'shippings' => CartShippingOptionResource::collection(static::getShippings()),
            ]
        ];
    }

    public static function getPayments(): Collection
    {
        return Payment::with('integration')
            ->where('sales_channel_id', SalesChannel::id())
            ->where('enabled', true)
            ->where('method', '!=', PaymentMethodEnum::Free)
            ->get();
    }

    public static function getProducts(): Collection
    {
        return Product::where('sales_channel_id', SalesChannel::id())
            ->where('enabled', true)
            ->get();
    }

    public static function getShippings(): Collection
    {
        return Shipping::where('sales_channel_id', SalesChannel::id())
            ->where('enabled', true)
            ->get();
    }
}
