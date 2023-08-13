<?php

namespace App\Actions\Engine\Order;

use App\Enums\Order\OrderStatusEnum;
use App\Exceptions\Order\PolicyException;
use App\Facades\Transaction;
use Exception;
use Illuminate\Validation\ValidationException;

class OrderCancelAction extends OrderAction
{
    public static function rules(): array
    {
        return [
            'order_id' => 'required|uuid|exists:orders,id',
        ];
    }

    public static function afterState(): array
    {
        return [OrderStatusEnum::Canceled];
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    protected function apply(): void
    {
        Transaction::cancel($this->target);

        $this->target->update([
            'version' => $this->target->version + 1,
            'status' => OrderStatusEnum::Canceled
        ]);
    }
}
