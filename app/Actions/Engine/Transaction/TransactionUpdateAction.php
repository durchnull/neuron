<?php

namespace App\Actions\Engine\Transaction;

use App\Enums\Transaction\TransactionStatusEnum;
use App\Facades\Order;
use Illuminate\Validation\Rules\Enum;

class TransactionUpdateAction extends TransactionAction
{

    public static function rules(): array
    {
        return [
            'status' => ['required', new Enum(TransactionStatusEnum::class)],
            'method' => 'nullable|string',
            'resource_data' => 'nullable|array'
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();

        if (!$this->silent) {
            Order::updateStatus($this->target->order);
        }
    }
}
