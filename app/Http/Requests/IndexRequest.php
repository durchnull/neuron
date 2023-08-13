<?php

namespace App\Http\Requests;

class IndexRequest extends ApiFormRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'nullable|array',
            'id.*' => 'nullable|uuid',
        ];
    }
}
