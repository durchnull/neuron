<?php

namespace App\Livewire\Admin\Engine;

use App\Actions\Engine\Order\OrderCancelAction;
use App\Actions\Engine\Order\OrderRefundAction;
use App\Actions\Engine\Transaction\TransactionUpdateAction;
use App\Enums\TriggerEnum;
use App\Exceptions\Order\PolicyException;
use App\Facades\SalesChannel;
use App\Models\Engine\Transaction;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Order extends Component
{
    public string $paymentProviderName;

    public array $mappedPaymentResource;

    public array $orderEvents;

    public bool $canRefund;

    public bool $canClose;


    public \App\Models\Engine\Order $order;

    /**
     * @throws Exception
     */
    public function mount(string $id)
    {
        $order = \App\Models\Engine\Order::with([
                'items',
                'items.product',
                'integrations'
            ])
            ->where('id', $id)
            ->first();

        if (!$order) {
            return $this->redirect(route('admin.home'));
        }

        $this->order = $order;

        $this->canRefund = \App\Facades\Order::can(new OrderRefundAction(
            $this->order,
            [],
            TriggerEnum::Admin
        ));

        $this->canClose = \App\Facades\Order::can(new OrderCancelAction(
            $this->order,
            [],
            TriggerEnum::Admin
        ));

        $this->orderEvents = DB::table('order_events')
            ->where('order_id', $id)
            ->get()
            ->toArray();

        $paymentProvider = \App\Facades\Integrations::getPaymentProvider($this->order->payment->integration);

        $this->paymentProviderName = class_basename($paymentProvider);

        try {
            $this->mappedPaymentResource = $paymentProvider->mapOrder(
                $this->order,
                $paymentProvider->getWebhookUrl($this->order->id, 'webhook-id')
            );
        } catch (Exception $exception) {
            $this->mappedPaymentResource = [$exception->getMessage()];
        }
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    public function updateTransactionStatus(string $transactionId, string $status)
    {
        /** @var Transaction $transaction */
        $transaction = Transaction::find($transactionId);

        SalesChannel::set($transaction->salesChannel);

        $transactionUpdateAction = new TransactionUpdateAction($transaction, [
            'status' => $status
        ], TriggerEnum::Admin);

        $transactionUpdateAction->trigger();

        $this->mount($this->order->id);
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    public function refund()
    {
        SalesChannel::set($this->order->salesChannel);

        $orderRefundAction = new OrderRefundAction($this->order, [
            'order_id' => $this->order->id
        ], TriggerEnum::Admin);

        $orderRefundAction->trigger();

        $this->mount($this->order->id);
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    public function close()
    {
        SalesChannel::set($this->order->salesChannel);

        $orderCancelAction = new OrderCancelAction($this->order, [
            'order_id' => $this->order->id
        ], TriggerEnum::Admin);

        $orderCancelAction->trigger();

        $this->mount($this->order->id);
    }

    public function render()
    {
        return view('livewire.admin.engine.order');
    }
}
