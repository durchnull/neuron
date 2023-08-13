<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\ApiFormRequest;

class TransactionRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'order_id' => 'required|uuid',
            'amazon_checkout_session_id' => 'nullable|uuid',
        ];
    }
}
