<?php

namespace App\Actions\Engine\Order;

use App\Contracts\Integration\PaymentProvider\AmazonPayServiceContract;
use App\Enums\Order\OrderStatusEnum;
use App\Facades\Customer;
use App\Facades\Integrations;
use App\Facades\Shipping;
use App\Models\Engine\Transaction;
use Exception;
use Illuminate\Support\Facades\Log;

class OrderAmazonPayCheckoutSessionCreateAction extends OrderAction
{
    public static function rules(): array
    {
        return [
            'order_id' => 'required|uuid|exists:orders,id',
            'amazon_checkout_session_id' => 'required|uuid',
        ];
    }

    /**
     * @throws Exception
     */
    protected function gate(array $attributes): void
    {
        parent::gate($attributes);

        $paymentProvider = Integrations::getPaymentProvider($this->target->payment->integration);

        if (!$paymentProvider instanceof AmazonPayServiceContract) {
            throw new \Exception('Cant instantiate correct payment provider');
        }

        if ($this->target->transactions->contains(fn(Transaction $transaction) => $transaction->resource_id === $attributes['amazon_checkout_session_id'])) {
            throw new \Exception('Transaction resource already exist');
        }
    }

    public static function afterState(): array
    {
        return [OrderStatusEnum::Open];
    }

    protected function apply(): void
    {
        $transaction = \App\Facades\Transaction::create($this->target, [], $this->validated['amazon_checkout_session_id']);
        $customerProfile = \App\Facades\Transaction::getCustomerProfile($transaction);

        try {
            // @todo customer, shipping_address, shipping method, which to set and which not?
            $this->target = Shipping::updateOrder($this->target, $customerProfile);
            $this->target = Customer::updateOrder($this->target, $customerProfile);
        } catch (Exception $exception) {
            // @todo return customerProfile shipping address if not working
            Log::error($exception->getMessage());
        }
    }
}
