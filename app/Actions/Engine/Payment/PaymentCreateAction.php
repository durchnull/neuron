<?php

namespace App\Actions\Engine\Payment;

use App\Enums\Payment\PaymentMethodEnum;
use App\Enums\TriggerEnum;
use App\Models\Engine\Payment;
use App\Models\Integration\PaymentProvider\NeuronPayment;
use App\Services\Integration\PaymentProvider\NeuronPaymentService;
use Illuminate\Validation\Rules\Enum;

class PaymentCreateAction extends PaymentAction
{
    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid',
            'integration_id' => 'required|uuid',
            'integration_type' => 'required|string', // @todo [validation] morphTo exists
            'enabled' => 'required|boolean',
            'name' => 'required|string',
            'method' => ['required', new Enum(PaymentMethodEnum::class)],
            'position' => 'required|integer|min:0',
            'description' => 'required|string',
            'default' => 'nullable|boolean',
        ];
    }

    protected function gate(array $attributes): void
    {
        // @todo [gate] refactor service container make
        switch ($attributes['integration_type']) {
            case NeuronPayment::class:
                if (!in_array($attributes['method'], array_map(fn(PaymentMethodEnum $paymentMethodEnum) => $paymentMethodEnum->value, NeuronPaymentService::getAllowedMethods()))) {
                    throw new \Exception('Method [' . $attributes['method'] . '] not allowed for integration type NeuronPayment');
                }
                break;
        }
    }

    protected function apply(): void
    {
        $payments = Payment::where('sales_channel_id', $this->validated['sales_channel_id'])->get();

        $targetCanBeDefault = $this->validated['method'] !== PaymentMethodEnum::Free->value;
        $enabledDefaultPaymentExists = $payments->contains(fn(Payment $payment) => $payment->default && $payment->enabled);
        $defaultIsSet = isset($this->validated['default']);

        if (!$enabledDefaultPaymentExists && $targetCanBeDefault && !$defaultIsSet) {
            $this->validated['default'] = true;
        }

        if (isset($this->validated['default']) && $this->validated['default'] === true) {
            $payments->filter(fn(Payment $payment) => $payment->enabled)
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
