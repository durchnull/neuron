<?php

namespace App\Livewire\Admin\Integration;

use App\Contracts\Integration\Mail\MailgunServiceContract;
use App\Facades\SalesChannel;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Mailgun extends Component
{
    public string $headline;

    public ?bool $integrationTest;

    #[Locked]
    public string $id;

    #[Rule('required|min:3')]
    public string $name;

    #[Rule('required|bool')]
    public bool $enabled;

    #[Rule('required|bool')]
    public bool $distributeOrder;

    #[Rule('required|bool')]
    public bool $refundOrder;

    #[Rule('required|string')]
    public string $domain;

    #[Rule('required|string')]
    public string $endpoint;

    #[Rule('required|string')]
    public string $secret;

    #[Rule('required|string')]
    public string $apiKey;

    #[Rule('required|string')]
    public string $orderTemplate;

    #[Rule('required|string')]
    public string $refundTemplate;

    #[Rule('required|string')]
    public string $from;

    #[Rule('required|string')]
    public string $orderSubject;

    #[Rule('required|string')]
    public string $refundSubject;

    #[Rule('nullable|string')]
    public string $sandboxTo;

    public \App\Models\Integration\Mail\Mailgun $mailgun;

    public function mount(string $id)
    {
        $mailgun = \App\Models\Integration\Mail\Mailgun::find($id);

        $this->headline = $mailgun->name;

        $this->id = $id;
        $this->name = $mailgun->name;
        $this->enabled = $mailgun->enabled;
        $this->distributeOrder = $mailgun->distribute_order;
        $this->refundOrder = $mailgun->refund_order;
        $this->domain = $mailgun->domain;
        $this->endpoint = $mailgun->endpoint;
        $this->secret = $mailgun->secret;
        $this->apiKey = $mailgun->api_key;
        $this->orderTemplate = $mailgun->order_template;
        $this->refundTemplate = $mailgun->refund_template;
        $this->from = $mailgun->from;
        $this->orderSubject = $mailgun->order_subject;
        $this->refundSubject = $mailgun->refund_subject;
        $this->sandboxTo = $mailgun->sandbox_to;
    }

    public function save()
    {
        $this->validate();

        \App\Models\Integration\Mail\Mailgun::where('id', $this->id)->update([
            'name' => $this->name,
            'enabled' => $this->enabled,
            'distribute_order' => $this->distributeOrder,
            'refund_order' => $this->refundOrder,
            'domain' => $this->domain,
            'endpoint' => $this->endpoint,
            'secret' => $this->secret,
            'api_key' => $this->apiKey,
            'order_template' => $this->orderTemplate,
            'refund_template' => $this->refundTemplate,
            'from' => $this->from,
            'order_subject' => $this->order_subject,
            'refund_subject' => $this->refund_subject,
            'sandbox_to' => $this->sandboxTo,
        ]);
    }

    public function testIntegration()
    {
        /** @var \App\Models\Integration\Mail\Mailgun $model */
        $model = \App\Models\Integration\Mail\Mailgun::with('salesChannel')->find($this->id);
        SalesChannel::set($model->salesChannel);
        $service = App::make(MailgunServiceContract::class, [Str::camel(class_basename(\App\Models\Integration\Mail\Mailgun::class)) => $model]);
        $this->integrationTest = $service->test();;
    }

    public function render()
    {
        return view('livewire.admin.integration.mailgun');
    }
}
