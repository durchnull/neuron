<?php

namespace App\Actions\Engine\Transaction;

use App\Enums\Transaction\TransactionStatusEnum;
use Illuminate\Validation\Rules\Enum;

class TransactionCreateAction extends TransactionAction
{

    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid|exists:sales_channels,id',
            'integration_id' => 'required|uuid',
            'integration_type' => 'required|string',
            'order_id' => 'required|uuid|exists:orders,id',
            'status' => ['required', new Enum(TransactionStatusEnum::class)],
            'method' => 'required|string',
            'resource_id' => 'required|string',
            'resource_data' => 'nullable|array',
            'webhook_id' => 'nullable|string',
            'checkout_url' => 'nullable|url',
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
