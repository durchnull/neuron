<?php

namespace App\Http\Controllers\Engine;

use App\Actions\Engine\Order\OrderAddItemAction;
use App\Actions\Engine\Order\OrderAmazonPayCheckoutSessionCreateAction;
use App\Actions\Engine\Order\OrderCreateAction;
use App\Actions\Engine\Order\OrderPlaceAction;
use App\Actions\Engine\Order\OrderRedeemCouponAction;
use App\Actions\Engine\Order\OrderRemoveCouponAction;
use App\Actions\Engine\Order\OrderRemoveItemAction;
use App\Actions\Engine\Order\OrderUpdateItemQuantityAction;
use App\Actions\Engine\Order\OrderUpdatePaymentAction;
use App\Actions\Engine\Order\OrderUpdateShippingAction;
use App\Contracts\Integration\PaymentProvider\AmazonPayServiceContract;
use App\Customer\CustomerProfile;
use App\Enums\TriggerEnum;
use App\Exceptions\Order\PolicyException;
use App\Facades\Customer;
use App\Facades\Integrations;
use App\Facades\Order;
use App\Facades\Shipping;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\CustomerProfileRequest;
use App\Http\Requests\Cart\OrderUpdatePaymentRequest;
use App\Http\Requests\Cart\TransactionRequest;
use App\Http\Resources\Engine\Cart\CartOptions;
use App\Http\Resources\Engine\Cart\CartResource;
use App\Http\Resources\Integration\PaymentProvider\AmazonPayButton;
use App\Http\Resources\Integration\PaymentProvider\AmazonPayCheckoutSession;
use App\Models\Engine\Transaction;
use App\Models\Integration\PaymentProvider\AmazonPay;
use App\Services\Integration\PaymentProvider\Resources\AmazonPayCheckoutSessionResource;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function options(Request $request): JsonResponse
    {
        return CartOptions::make();
    }

    /**
     * @param  Request  $request
     * @param  string  $id
     * @return JsonResource
     */
    public function show(Request $request, string $id): JsonResource
    {
        $order = Order::get();

        return new CartResource($order);
    }

    /**
     * @param  Request  $request
     * @return CartResource
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    public function create(Request $request): CartResource
    {
        $order = $this->action(
            new OrderCreateAction(
                new \App\Models\Engine\Order(),
                $request->all(),
                TriggerEnum::Api
            )
        );

        return new CartResource($order);
    }

    /**
     * @param  Request  $request
     * @return CartResource
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function updateShipping(Request $request): CartResource
    {
        $order = $this->action(
            new OrderUpdateShippingAction(
                Order::get(),
                $request->all(),
                TriggerEnum::Api
            )
        );

        return new CartResource($order);
    }

    /**
     * @param  OrderUpdatePaymentRequest  $request
     * @return CartResource
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function updatePayment(OrderUpdatePaymentRequest $request): CartResource
    {
        $order = $this->action(
            new OrderUpdatePaymentAction(
                Order::get(),
                $request->all(),
                TriggerEnum::Api
            )
        );

        return new CartResource($order);
    }

    /**
     * @param  Request  $request
     * @return CartResource
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    public function addItem(Request $request): CartResource
    {
        $order = $this->action(
            new OrderAddItemAction(
                Order::get(),
                $request->all(),
                TriggerEnum::Api
            )
        );

        return new CartResource($order);
    }

    /**
     * @param  Request  $request
     * @return CartResource
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function updateItem(Request $request): CartResource
    {
        $order = $this->action(
            new OrderUpdateItemQuantityAction(
                Order::get(),
                $request->all(),
                TriggerEnum::Api
            )
        );

        return new CartResource($order);
    }

    /**
     * @param  Request  $request
     * @return CartResource
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    public function removeItem(Request $request): CartResource
    {
        $order = $this->action(
            new OrderRemoveItemAction(
                Order::get(),
                $request->all(),
                TriggerEnum::Api
            )
        );

        return new CartResource($order);
    }

    /**
     * @param  Request  $request
     * @return CartResource|JsonResponse
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function place(Request $request): CartResource|JsonResponse
    {
        /** @var \App\Models\Engine\Order $order */
        $order = $this->action(
            new OrderPlaceAction(
                Order::get(),
                $request->all(),
                TriggerEnum::Api
            )
        );

        return new CartResource($order);
    }

    /**
     * @param  CustomerProfileRequest  $request
     * @return CartResource|JsonResponse
     */
    public function updateCustomer(CustomerProfileRequest $request): CartResource|JsonResponse
    {
        $order = Order::get();

        // @todo middleware?
        if ($order->payment->integration_type === AmazonPay::class) {
            return response()->json([
                'message' => 'Manual customer update not allowed'
            ], 400);
        }

        $customerProfile = new CustomerProfile($request->validated());
        
        $order = Shipping::updateOrder($order, $customerProfile);
        $order = Customer::updateOrder($order, $customerProfile);

        return new CartResource($order);
    }

    /**
     * @param  Request  $request
     * @return CartResource
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function redeemCoupon(Request $request): CartResource
    {
        /** @var \App\Models\Engine\Order $order */
        $order = $this->action(
            new OrderRedeemCouponAction(
                Order::get(),
                $request->all(),
                TriggerEnum::Api
            )
        );

        return new CartResource($order);
    }

    /**
     * @param  Request  $request
     * @return CartResource
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function removeCoupon(Request $request): CartResource
    {
        /** @var \App\Models\Engine\Order $order */
        $order = $this->action(
            new OrderRemoveCouponAction(
                Order::get(),
                $request->all(),
                TriggerEnum::Api
            )
        );

        return new CartResource($order);
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    public function transactionCreate(TransactionRequest $request): JsonResponse|JsonResource
    {
        if (!$amazonCheckoutSessionId = $request->validated('amazon_checkout_session_id')) {
            return response()->json('Invalid data', 400);
        }

        $order = Order::get();

        /** @var \App\Models\Engine\Order $order */
        $order = $this->action(
            new OrderAmazonPayCheckoutSessionCreateAction(
                $order,
                [
                    'order_id' => $order->id,
                    'amazon_checkout_session_id' => $amazonCheckoutSessionId,
                ],
                TriggerEnum::Api
            )
        );

        return new CartResource($order);
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    public function transactionUpdate(TransactionRequest $request): JsonResponse|JsonResource
    {
        if (!$amazonCheckoutSessionId = $request->validated('amazon_checkout_session_id')) {
            return response()->json('Invalid data', 400);
        }

        $order = Order::get();

        /** @var \App\Models\Engine\Order $order */
        $order = $this->action(
            new OrderAmazonPayCheckoutSessionUpdateAction(
                $order,
                [
                    'order_id' => $order->id,
                    'amazon_checkout_session_id' => $amazonCheckoutSessionId,
                ],
                TriggerEnum::Api
            )
        );

        return new CartResource($order);
    }

    /**
     * @throws Exception
     */
    public function amazonPayButton(Request $request): JsonResource
    {
        $order = Order::get();

        if ($order->payment->integration_type !== AmazonPay::class) {
            throw new Exception('Invalid payment'); // @todo
        }

        return new AmazonPayButton($order);
    }
}
