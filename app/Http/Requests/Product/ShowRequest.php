<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\ApiFormRequest;

class ShowRequest extends ApiFormRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|uuid|exists:products',
        ];
    }
}
