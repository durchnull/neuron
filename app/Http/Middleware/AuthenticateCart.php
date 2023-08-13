<?php

namespace App\Http\Middleware;

use App\Facades\Order;
use App\Facades\SalesChannel;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateCart
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @throws ValidationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request = $this->handleSalesChannel($request, $next);

        if (in_array($request->route()->getName(), ['cart.options', 'cart.create'])) {
            return $next($request);
        }

        if (in_array($request->route()->getName(), ['cart.item.update', 'cart.item.remove'])) {
            $request->merge([
                'order_item_id' => $request->post('cart_item_id')
            ]);

            $request->request->remove('cart_item_id');
        }

        if ($request->route()->getName() === 'cart.show') {
            $orderId = $request->route('id');
        } else {
            $orderId = $request->post('cart_id');
            $request->request->remove('cart_id');
        }

        $request = $this->handleOrder(
            $request,
            $next,
            $orderId
        );

        return $next($request);
    }

    /**
     * @throws ValidationException
     */
    protected function handleSalesChannel(Request $request, Closure $next): Request
    {
        $validator = Validator::make([
            'token' => $request->bearerToken()
        ], [
            'token' => 'required|exists:sales_channels,cart_token'
        ]);

        if ($validator->fails()) {
            $data = [
                'failed' => $validator->failed(),
                'messages' => $validator->getMessageBag(),
            ];

            throw new HttpResponseException(response()->json($data, 401));
        }

        SalesChannel::setByCartToken($validator->validated()['token']);

        $request->merge([
            'sales_channel_id' => SalesChannel::id()
        ]);

        return $request;
    }

    /**
     * @throws ValidationException
     */
    protected function handleOrder(Request $request, Closure $next, ?string $orderId): Request
    {
        $validator = Validator::make([
            'order_id' => $orderId
        ], [
            'order_id' => 'required|uuid|exists:orders,id,sales_channel_id,' . SalesChannel::id()
        ]);

        if ($validator->fails()) {
            $data = [
                'failed' => $validator->failed(),
                'messages' => $validator->getMessageBag(),
            ];

            throw new HttpResponseException(response()->json($data, 401));
        }

        $orderId = $validator->validated()['order_id'];

        Order::setById($orderId);
        Order::set(Order::update(Order::get()));

        if (!Order::open() && $request->route()->getName() !== 'cart.show') {
            // @todo [response] status code and exception class
            throw new HttpResponseException(response()->json([
                'code' => 'cart-not-open'
            ], 409));
        }

        $request->merge([
            'order_id' => $orderId
        ]);

        return $request;
    }
}
