<?php

namespace App\Http\Middleware;

use App\Facades\Merchant;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMerchant
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @throws ValidationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $validator = Validator::make([
            'token' => $request->bearerToken()
        ], [
            'token' => 'required|exists:merchants,token'
        ]);

        if ($validator->fails()) {
            $data = [
                'failed' => $validator->failed(),
                'messages' => $validator->getMessageBag(),
            ];

            throw new HttpResponseException(response()->json($data, 401));
        }

        Merchant::setByToken($validator->validated()['token']);

        $request->merge([
            'merchant_id' => Merchant::id()
        ]);

        return $next($request);
    }
}
