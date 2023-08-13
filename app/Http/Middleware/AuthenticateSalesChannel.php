<?php

namespace App\Http\Middleware;

use App\Facades\SalesChannel;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateSalesChannel
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
            'token' => 'required|exists:sales_channels,token'
        ]);

        if ($validator->fails()) {
            $data = [
                'failed' => $validator->failed(),
                'messages' => $validator->getMessageBag(),
            ];

            throw new HttpResponseException(response()->json($data, 401));
        }

        SalesChannel::setByToken($validator->validated()['token']);

        $request->merge([
            'sales_channel_id' => SalesChannel::get()->id
        ]);

        return $next($request);
    }
}
