<?php

namespace App\Actions\Engine\Order;

use App\Enums\Integration\IntegrationTypeEnum;
use App\Enums\Order\OrderStatusEnum;
use App\Enums\Order\PolicyReasonEnum;
use App\Enums\Payment\PaymentMethodEnum;
use App\Exceptions\Order\PolicyException;
use App\Facades\Integrations;
use App\Facades\Transaction;
use Exception;
use Illuminate\Validation\ValidationException;

class OrderRefundAction extends OrderAction
{
    public static function rules(): array
    {
        return [
            'order_id' => 'required|uuid|exists:orders,id',
        ];
    }

    public function gate(array $attributes): void
    {
        parent::gate($attributes);

        if ($this->target->payment->method === PaymentMethodEnum::Free) {
            $this->addPolicy(PolicyReasonEnum::PaymentIsFree);
        }

        // @todo [gate] Only possible if there are no pending and authorized transactions?
    }

    public static function afterState(): array
    {
        return [OrderStatusEnum::Refunded];
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    protected function apply(): void
    {
        Transaction::refundOrder($this->target);
        Integrations::refundOrder($this->target, [IntegrationTypeEnum::Mail]);

        $this->target->update([
            'version' => $this->target->version + 1,
            'status' => OrderStatusEnum::Refunded
        ]);
    }
}
