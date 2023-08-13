<?php

namespace App\Services\Engine;

use App\Actions\Engine\Order\OrderAction;
use App\Actions\Engine\Order\OrderAddCartRuleAction;
use App\Actions\Engine\Order\OrderRemoveCartRuleAction;
use App\Actions\Engine\Order\OrderRemoveCouponAction;
use App\Actions\Engine\Order\OrderRemoveItemAction;
use App\Actions\Engine\Order\OrderUpdateItemAction;
use App\Actions\Engine\Order\OrderUpdatePaymentAction;
use App\Actions\Engine\Order\OrderUpdateStatusAction;
use App\Contracts\Engine\OrderServiceContract;
use App\Contracts\Engine\SalesChannelContract;
use App\Enums\Order\OrderStatusEnum;
use App\Enums\Payment\PaymentMethodEnum;
use App\Enums\Transaction\TransactionStatusEnum;
use App\Enums\TriggerEnum;
use App\Exceptions\Order\PolicyException;
use App\Generators\OrderNumberGenerator;
use App\Models\Engine\CartRule;
use App\Models\Engine\Coupon;
use App\Models\Engine\Order;
use App\Models\Engine\Item;
use App\Models\Engine\Payment;
use App\Models\Engine\Product;
use App\Models\Engine\SalesChannel;
use App\Models\Engine\Transaction;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderService implements OrderServiceContract
{
    // <editor-fold desc="Header">

    protected ?Order $order;

    public function __construct(
        protected SalesChannelContract $salesChannelService,
        protected OrderNumberGenerator $orderNumberGenerator,
        protected array $statusFlow,
        protected array $actionFlow,
    ) {
        $this->order = null;
    }

    // </editor-fold>

    // <editor-fold desc="Order">

    /**
     * @return Order
     * @throws Exception
     */
    public function get(): Order
    {
        if ($this->order === null) {
            throw new Exception('Order has not been initialized');
        }

        return $this->order;
    }

    public function open(): bool
    {
        return $this->order->status === OrderStatusEnum::Open;
    }

    /**
     * @param  Order  $order
     * @return OrderServiceContract
     * @throws Exception
     */
    public function set(Order $order): OrderServiceContract
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @param  string  $id
     * @return OrderServiceContract
     * @throws Exception
     */
    public function setById(string $id): OrderServiceContract
    {
        /** @var Order $order */
        $order = Order::with([
            'items.product',
            'cartRules.rule.condition',
            'coupons.rule.condition',
            'transactions',
            'shipping',
            'payment',
            'customer',
            'shippingAddress',
            'billingAddress',
        ])
            ->find($id);

        if ($order) {
            return $this->set($order);
        }

        throw new Exception('Order has not been found ' . $id);
    }

    /**
     * Read and sum the totals of the order without further calculations
     *
     * @param  Order  $order
     * @return array
     */
    public function getTotals(Order $order): array
    {
        $itemsAmount = $order->items->sum('total_amount');
        $itemsDiscountAmount = $order->items->sum('discount_amount');
        $shippingAmount = $order->shipping_amount;
        $shippingDiscountAmount = $order->shipping_discount_amount;

        return [
            'amount' => $itemsAmount - $itemsDiscountAmount + $shippingAmount - $shippingDiscountAmount,
            'items_amount' => $itemsAmount,
            'items_discount_amount' => $itemsDiscountAmount,
            'shipping_amount' => $shippingAmount,
            'shipping_discount_amount' => $shippingDiscountAmount,
        ];
    }

    /**
     * @throws Exception
     */
    public function generateOrderNumber(): string
    {
        $orderNumber = null;

        while ($orderNumber === null) {
            $orderNumber = $this->orderNumberGenerator->generate();

            if (Order::where('order_number', $orderNumber)->exists()) {
                $orderNumber = null;
            }
        }

        return $this->orderNumberGenerator->generate();
    }

    // </editor-fold>

    // <editor-fold desc="Flow">

    /**
     * @param  OrderAction  $action
     * @return bool
     */
    public function can(OrderAction $action): bool
    {
        $actionIsAllowed = in_array(
            get_class($action),
            $this->actionFlow[$action->target()->status->value]
        );

        $stateIsUnchanged = in_array(
            $action->target()->status->value,
            array_map(
                fn(OrderStatusEnum $orderStatusEnum) => $orderStatusEnum->value,
                $action::afterState()
            )
        );

        // @todo [implementation]
        $stateTransitionIsAllowed = true;
        /*
        $stateTransitionIsAllowed = in_array(
            $action->target()->status->value,
            $this->statusFlow[$action::afterState()->value]
        );
        */

        if (!($actionIsAllowed && ($stateIsUnchanged || $stateTransitionIsAllowed))) {
            Log::channel('order')->info('Action is allowed           ' . ($actionIsAllowed ? 'yes' : 'no'));
            Log::channel('order')->info('State is unchanged          ' . ($stateIsUnchanged ? 'yes' : 'no'));
            Log::channel('order')->info('State transition is allowed ' . ($stateTransitionIsAllowed ? 'yes' : 'no'));
        }

        return $actionIsAllowed && ($stateIsUnchanged || $stateTransitionIsAllowed);
    }

    // </editor-fold>

    // <editor-fold desc="Updating">

    /**
     * @param  Order  $order
     * @return Order
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function update(Order $order): Order
    {
        // @todo [test]
        $order = $this->updateCartRules($order);
        $order = $this->updateCoupons($order);
        $order = $this->updateItems($order);

        return $order;
    }

    /**
     * @param  Order  $order
     * @return Order
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function updateItems(Order $order): Order
    {
        $actions = [];

        /** @var Item $item */
        foreach ($order->items as $item) {

            // @todo [cache]
            /** @var Product $product */
            $product = $item->product()->first();

            if (!$item->product->enabled) {
                // @todo [test]
                $actions[] = new OrderRemoveItemAction(
                    $order,
                    [
                        'order_id' => $order->id,
                        'order_item_id' => $item->id,
                    ],
                    TriggerEnum::App
                );
            } elseif ($item->product_version !== $product->version) {
                if ($item->unit_amount < $product->getPrice()) {

                    if (\App\Facades\SalesChannel::removeItemsOnPriceIncrease()) {
                        // @todo [test]
                        $actions[] = new OrderRemoveItemAction(
                            $order,
                            [
                                'order_id' => $order->id,
                                'order_item_id' => $item->id,
                            ],
                            TriggerEnum::App
                        );
                    }
                } elseif ($item->unit_amount > $product->getPrice()) {
                    // @todo [test]
                    $actions[] = new OrderUpdateItemAction(
                        $order,
                        [
                            'order_id' => $order->id,
                            'order_item_id' => $item->id,
                            'product_version' => $product->version,
                            'total_amount' => $product->getPrice() * $item->quantity,
                            'unit_amount' => $product->getPrice(),
                        ],
                        TriggerEnum::App
                    );
                }
            }
        }

        /** @var OrderAction $action */
        foreach ($actions as $action) {
            $action->trigger();

            if ($action->denied()) {
                throw new Exception(json_encode($action->policies()));
            } else {
                $order = $action->target();
            }
        }

        return $order;
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    public function updateCartRules(Order $order): Order
    {
        // @todo [cache]
        $cartRules = CartRule::where([
                'sales_channel_id' => $order->sales_channel_id,
                'enabled' => true
            ])
            ->whereNotIn('id', $order->cartRules->pluck('id'))
            ->get();

        $actions = [];

        /** @var CartRule $orderCartRule */
        foreach ($order->cartRules as $orderCartRule) {
            if (! $orderCartRule->enabled) {
                // @todo [test]
                $actions[] = new OrderRemoveCartRuleAction($order, [
                    'order_id' => $order->id,
                    'cart_rule_id' => $orderCartRule->id
                ], TriggerEnum::App);
            }
        }

        /** @var CartRule $cartRule */
        foreach ($cartRules as $cartRule) {
            // @todo [test]
            $actions[] = new OrderAddCartRuleAction($order, [
                'order_id' => $order->id,
                'cart_rule_id' => $cartRule->id
            ], TriggerEnum::App);
        }

        /** @var OrderAction $action */
        foreach ($actions as $action) {
            $action->trigger();

            if ($action->denied()) {
                throw new Exception(json_encode($action->policies()));
            } else {
                $order = $action->target();
            }
        }

        return $order;
    }

    /**
     * @throws Exception
     */
    public function updateCoupons(Order $order): Order
    {
        $actions = [];

        /** @var Coupon $coupon */
        foreach ($order->coupons as $coupon) {
            if (! $coupon->enabled) {
                // @todo [test] should remove coupon and referenced items
                $actions[] = new OrderRemoveCouponAction($order, [
                    'order_id' => $order->id,
                    'code' => $coupon->code
                ], TriggerEnum::App);
            }
        }

        /** @var OrderAction $action */
        foreach ($actions as $action) {
            $action->trigger();

            if ($action->denied()) {
                throw new Exception(json_encode($action->policies()));
            } else {
                $order = $action->target();
            }
        }

        return $order;
    }

    /**
     * @param  Order  $order
     * @return Order
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function updateStatus(Order $order): Order
    {
        return match ($order->status) {
            OrderStatusEnum::Placing => $this->updateStatusFromPlacing($order),
            OrderStatusEnum::Accepted => $this->updateStatusFromAccepted($order),
            default => $order,
        };
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    protected function updateStatusFromPlacing(Order $order): Order
    {
        $transaction = $order->transactions->sortByDesc('created_at')->first(); // @todo

        if (!$transaction) {
            throw new Exception('Transaction not found in placed order');
        }

        $orderStatus = match ($transaction->status) {
            TransactionStatusEnum::Created => OrderStatusEnum::Placing,
            TransactionStatusEnum::Pending => OrderStatusEnum::Accepted,
            TransactionStatusEnum::Paid, TransactionStatusEnum::Authorized => OrderStatusEnum::Confirmed,
            TransactionStatusEnum::Failed, TransactionStatusEnum::Canceled => OrderStatusEnum::Open,
            TransactionStatusEnum::Refunded => throw new Exception('Status error'),
            default => throw new Exception('Status unknown'),
        };

        if ($orderStatus instanceof OrderStatusEnum) {
            $action = new OrderUpdateStatusAction($order, [
                'order_id' => $order->id,
                'status' => $orderStatus
            ], TriggerEnum::App);

            $action->trigger();

            $order = $action->target();
        }

        return $order;
    }

    /**
     * @param  Order  $order
     * @return Order
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    protected function updateStatusFromAccepted(Order $order): Order
    {
        $transaction = $order->transactions->sortByDesc('created_at')->first(); // @todo

        if (!$transaction) {
            throw new Exception('Transaction not found in placed order');
        }

        $orderStatus = match ($transaction->status) {
            TransactionStatusEnum::Paid, TransactionStatusEnum::Authorized => OrderStatusEnum::Confirmed,
            TransactionStatusEnum::Failed, TransactionStatusEnum::Canceled => OrderStatusEnum::Canceled,
            TransactionStatusEnum::Pending => null,
            default => throw new Exception('Status not handled ' . $transaction->status->value),
        };

        if ($orderStatus instanceof OrderStatusEnum) {
            $action = new OrderUpdateStatusAction($order, [
                'order_id' => $order->id,
                'status' => $orderStatus
            ], TriggerEnum::App);

            $action->trigger();

            $order = $action->target();
        }

        return $order;
    }

    /**
     * @param  Order  $order
     * @return Order
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function updatePayment(Order $order): Order
    {
        $updatedPaymentId = null;
        $orderAmount = $this->getTotals($order)['amount'];

        if ($orderAmount === 0 && $order->payment->method !== PaymentMethodEnum::Free) {
            // @todo [payment] free payment method must always exist
            $freePaymentId = Payment::where('sales_channel_id', $order->sales_channel_id)
                ->where('method', PaymentMethodEnum::Free)
                ->value('id');

            if ($freePaymentId === null) {
                throw new Exception('Free payment method for sales channel ' . $order->sales_channel_id . ' is missing');
            }

            $updatedPaymentId = $freePaymentId;
        } elseif ($orderAmount > 0 && $order->payment->method === PaymentMethodEnum::Free) {
            // @todo [test]
            // @todo [payment] default payment method must always exist
            $updatedPaymentId = Payment::where('sales_channel_id', $order->sales_channel_id)
                ->where('default', true)
                ->whereNot('method', PaymentMethodEnum::Free)
                ->first()
                ->id;

            if ($updatedPaymentId === null) {
                throw new Exception('Default payment method for sales channel ' . $order->sales_channel_id . ' is not set');
            }
        }

        if ($updatedPaymentId) {
            $action = new OrderUpdatePaymentAction($order, [
                'order_id' => $order->id,
                'payment_id' => $updatedPaymentId
            ], TriggerEnum::App);

            $action->trigger();

            $order = $action->target();
        }

        return $order;
    }

    // </editor-fold>
}
