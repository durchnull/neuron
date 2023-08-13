<?php

namespace App\Actions\Integration\Mail\Mailgun;

class MailgunCreateAction extends MailgunAction
{

    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid|exists:sales_channels,id',
            'enabled' => 'required|boolean',
            'distribute_order' => 'required|boolean',
            'refund_order' => 'required|boolean',
            'name' => 'required|string|min:3',
            'domain' => 'required|string',
            'endpoint' => 'required|in:api.mailgun.net',
            'secret' => 'required|string',
            'api_key' => 'required|string',
            'order_template' => 'required|string',
            'refund_template' => 'required|string',
            'from' => 'required|email',
            'order_subject' => 'required|string',
            'refund_subject' => 'required|string',
            'sandbox_to' => 'nullable|email',
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
