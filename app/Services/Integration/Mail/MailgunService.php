<?php

namespace App\Services\Integration\Mail;

use App\Contracts\Integration\Mail\MailgunServiceContract;
use App\Enums\Integration\IntegrationResourceStatusEnum;
use App\Models\Engine\Order;
use App\Models\Engine\Item;
use App\Models\Integration\OrderIntegration;
use App\Models\Integration\Mail\Mailgun;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mailgun\Model\Domain\ShowResponse;
use Mailgun\Model\Message\SendResponse;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * @link https://github.com/mailgun/mailgun-php
 */
class MailgunService implements MailgunServiceContract
{
    protected \Mailgun\Mailgun $client;

    public function __construct(
        protected Mailgun $mailgun
    ) {
        $this->client = \Mailgun\Mailgun::create(
            $this->mailgun->secret,
            'https://' . $this->mailgun->endpoint,
        );
    }

    public static function getClientVersion(): string
    {
        return '3.6.1';
    }

    public function test(): bool
    {
        /** @var ShowResponse $response */
        try {
            $response = $this->client->domains()->show($this->mailgun->domain);
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function distributeOrder(Order $order): void
    {
        if ($this->mailgun->enabled && $this->mailgun->distribute_order) {
            $resourceId = null;
            $status = IntegrationResourceStatusEnum::Created;

            try {
                /** @var SendResponse $response */
                $response = $this->client
                    ->messages()
                    ->send($this->mailgun->domain, $this->mapOrder($order));
                $resourceId = $response->getId();
                $status = IntegrationResourceStatusEnum::Distributed;
            } catch (ClientExceptionInterface $exception) {
                Log::channel('integration')->error('Mailgun [' . $exception->getCode() . '] ' . $exception->getMessage());
                $status = IntegrationResourceStatusEnum::DistributedFailed;
            }

            OrderIntegration::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'integration_id' => $this->mailgun->id,
                    'integration_type' => get_class($this->mailgun),
                ],
                [
                    'resource_id' => $resourceId,
                    'status' => $status,
                ]
            );
        }
    }

    public function mapOrder(Order $order): array
    {
        return [
            'from' => $this->mailgun->from,
            'to' => ($this->isSandbox() && !empty($this->mailgun->sandbox_to))
                ? $this->mailgun->sandbox_to
                : $order->customer->email,
            'subject' => $this->mapOrderSubject($order),
            'template' => $this->mailgun->order_template,
            'h:X-Mailgun-Variables' => json_encode($this->getOrderTemplateVariables($order))
        ];
    }

    public function mapRefund(Order $order): array
    {
        return [
            'from' => $this->mailgun->from,
            'to' => ($this->isSandbox() && !empty($this->mailgun->sandbox_to))
                ? $this->mailgun->sandbox_to
                : $order->customer->email,
            'subject' => $this->mapRefundedSubject($order),
            'template' => $this->mailgun->refund_template,
            'h:X-Mailgun-Variables' => json_encode($this->getRefundedTemplateVariables($order))
        ];
    }

    protected function mapOrderSubject(Order $order): string
    {
        return str_replace('{order_number}', $order->order_number, $this->mailgun->subject);
    }

    protected function mapRefundedSubject(Order $order): string
    {
        return str_replace('{order_number}', $order->order_number, $this->mailgun->refund_subject);
    }

    protected function getOrderTemplateVariables(Order $order): array
    {
        return [
            // @todo [implementation]
            'order_number' => $order->order_number,
            'amount' => $order->amount,
            'items' => $order->items->map(fn(Item $item) => [
                'product' => $item->product->name,
                'amount' => $item->total_amount,
                'quantity' => $item->quantity,
            ]),
        ];
    }

    protected function getRefundedTemplateVariables(Order $order): array
    {
        return [
            // @todo [implementation]
            'order_number' => $order->order_number,
            'amount' => $order->amount,
            'items' => $order->items->map(fn(Item $item) => [
                'product' => $item->product->name,
                'amount' => $item->total_amount,
                'quantity' => $item->quantity,
            ]),
        ];
    }

    public function isSandbox(): bool
    {
        return Str::contains($this->mailgun->domain, 'sandbox');
    }

    /**
     * @throws Exception
     */
    public function refundOrder(Order $order): void
    {
        if ($this->mailgun->enabled && $this->mailgun->refund_order) {
            try {
                /** @var SendResponse $response */
                $response = $this->client
                    ->messages()
                    ->send($this->mailgun->domain, $this->mapRefund($order));
            } catch (ClientExceptionInterface $exception) {
                throw new Exception($exception->getMessage(), $exception->getCode());
            }
        }
    }
}
