<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class ApiFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  Validator  $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        Log::channel('api')->error(class_basename($this));
        Log::channel('api')->error(json_encode($validator->failed()));

        $data = [
            'failed' => $validator->failed(),
            'messages' => $validator->getMessageBag(),
        ];

        throw new HttpResponseException(response()->json($data, 422));
    }
}
