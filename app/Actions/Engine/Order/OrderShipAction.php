<?php

namespace App\Actions\Engine\Order;

use App\Enums\Order\OrderStatusEnum;
use App\Exceptions\Order\PolicyException;
use Exception;
use Illuminate\Validation\ValidationException;

// @todo [test]
class OrderShipAction extends OrderAction
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
    }

    public static function afterState(): array
    {
        return [OrderStatusEnum::Shipped];
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    protected function apply(): void
    {
        // @todo [implementation]
        //Transaction::shipOrder($this->target);

        $this->target->update([
            'version' => $this->target->version + 1,
            'status' => OrderStatusEnum::Shipped
        ]);
    }
}
