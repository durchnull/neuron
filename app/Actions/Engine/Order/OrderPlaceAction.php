<?php

namespace App\Actions\Engine\Order;

use App\Enums\Order\OrderStatusEnum;
use App\Enums\Order\PolicyReasonEnum;
use App\Enums\Payment\PaymentMethodEnum;
use App\Exceptions\Order\PolicyException;
use App\Facades\Coupon;
use App\Facades\Order;
use App\Facades\Stock;
use App\Facades\Transaction;
use App\Models\Engine\Customer;
use App\Services\Engine\CouponService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderPlaceAction extends OrderAction
{
    public static function rules(): array
    {
        return [
            'order_id' => 'required|uuid|exists:orders,id',
            'payment_data' => 'nullable|array', // @todo [test] required_array_keys:card_token
        ];
    }

    public function gate(array $attributes): void
    {
        parent::gate($attributes);

        if ($this->target->items->isEmpty()) {
            $this->addPolicy(PolicyReasonEnum::CartIsEmpty);
        }

        if (!$this->target->customer instanceof Customer) {
            $this->addPolicy(PolicyReasonEnum::CustomerNotSet);
        }

        // @todo [test]
        if (Order::getTotals($this->target)['amount'] === 0 && $this->target->payment->method !== PaymentMethodEnum::Free) {
            $this->addPolicy(PolicyReasonEnum::PaymentIsFree);
        }

        // @todo if proxy(amazon) payment then transaction is required
    }

    public static function afterState(): array
    {
        return [OrderStatusEnum::Placing];
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    protected function apply(): void
    {
        $transaction = Transaction::place(
            $this->target,
            $this->validated['payment_data'] ?? [],
        );

        $this->target->update([
            'version' => $this->target->version + 1,
            'status' => OrderStatusEnum::Placing,
            'ordered_at' => now(),
        ]);

        // @todo save necessary?
        // $this->target->transactions()->save($transaction);

        $this->target->setRelation('transactions', collect([$transaction])->concat($this->target->transactions));

        // @todo [test]
        $this->target->setRelation('customer', \App\Facades\Customer::setNew($this->target->customer, false));

        Stock::queueOrder($this->target);

        $this->target = Coupon::chargeCredit($this->target);

        try {
            $this->target = Order::updateStatus($this->target);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
        }
    }
}
