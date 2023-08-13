<?php

namespace App\Actions\Engine\Payment;

use App\Enums\TriggerEnum;
use App\Models\Engine\Payment;

class PaymentUpdateAction extends PaymentAction
{

    public static function rules(): array
    {
        return [
            'name' => 'nullable|string',
            'enabled' => 'nullable|boolean',
            'position' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'default' => 'nullable|boolean',
        ];
    }

    protected function apply(): void
    {
        // @todo [test]
        if (isset($this->validated['default']) && $this->validated['default'] === true) {
            Payment::where('sales_channel_id', $this->target->salesChannel->id)
                ->whereNot('id', $this->target->id)
                ->get()
                ->each(function (Payment $payment) {
                    $updatePaymentAction = new PaymentUpdateAction($payment, [
                        'default' => false,
                    ], TriggerEnum::App);

                    $updatePaymentAction->trigger();
                });
        }

        $this->target->fill($this->validated)->save();
    }
}
