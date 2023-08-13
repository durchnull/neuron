<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\ApiFormRequest;

class OrderUpdatePaymentRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'order_id' => 'required|uuid|exists:orders,id',
            'payment_id' => 'nullable|uuid|exists:payments,id', // @todo [validation] not "Free"
        ];
    }
}
