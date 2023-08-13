<?php

namespace App\Services\Integration\PaymentProvider;

use App\Actions\Integration\PaymentProvider\Paypal\PaypalUpdateAction;
use App\Contracts\Integration\PaymentProvider\PaypalServiceContract;
use App\Enums\Payment\PaymentMethodEnum;
use App\Enums\TriggerEnum;
use App\Exceptions\Order\PolicyException;
use App\Models\Engine\Order;
use App\Models\Integration\Integration;
use App\Models\Integration\PaymentProvider\Paypal;
use App\Services\Integration\PaymentProvider\Resources\PaypalOrderResource;
use App\Services\Integration\PaymentProvider\Resources\Resource;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaypalService implements PaypalServiceContract
{
    // <editor-fold desc="Header">

    protected string $apiUrl;

    /**
     * @throws ValidationException
     * @throws PolicyException
     */
    public function __construct(
        protected Paypal $paypal
    ) {
        $this->apiUrl = 'https://api-m.sandbox.paypal.com';

        if ($this->shouldRefreshAccessToken()) {
            $this->refreshAccessToken();
        }
    }

    public static function getClientVersion(): string
    {
        return 'v2';
    }

    public function test(): bool
    {
        $response = $this->get('v1/identity/openidconnect/userinfo?schema=openid');

        return $response->successful();
    }

    protected function getBaseRequest(): PendingRequest
    {
        return Http::withToken($this->paypal->access_token)
            ->asForm();
    }

    protected function get(string $path): Response
    {
        if (! Str::startsWith($path, '/')) {
            $path = '/' . $path;
        }

        return $this->getBaseRequest()->get($this->apiUrl . $path);
    }

    protected function post(string $path, array $data = []): Response
    {
        if (! Str::startsWith($path, '/')) {
            $path = '/' . $path;
        }

        return $this->getBaseRequest()->post($this->apiUrl . $path, $data);
    }

    public function getResource(string $id): Resource
    {
        return new PaypalOrderResource(
            $this->get('v2/checkout/orders/' . $id)
        );
    }

    public function createResource(Order $order, string $webhookId, array $resourceData = []): Resource
    {
        return new PaypalOrderResource(
            $this->post('v2/checkout/orders', $this->mapOrder($order, $this->getWebhookUrl($order->id, $webhookId)))
        );
    }

    public function updateResource(string $id, Order $order, array $resourceData = []): Resource
    {
        throw new Exception('Not implemented');
    }

    public function placeResource(string $id, Order $order, array $resourceData = []): Resource
    {
        throw new Exception('Not implemented');
    }

    public function refundResource(string $id): Resource
    {
        throw new Exception('Not implemented');
    }

    public function mapOrder(Order $order, string $webhookUrl): array
    {
        return [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                'reference_id' => $order->id,
                'amount' => $this->makeAmount($order->amount, $this->paypal->salesChannel->currency_code),
            ],
        ];
    }

    public function getWebhookUrl(string $orderId, string $webhookId): string
    {
        return ''; // @todo
    }

    public static function getAllowedMethods(): array
    {
        return [
            PaymentMethodEnum::Proxy
        ];
    }

    public function getIntegration(): Integration
    {
        return $this->paypal;
    }

    /**
     * @return Response
     */
    public function getOAuthTokenResponse(): Response
    {
        return Http::asForm()
            ->withBasicAuth($this->paypal->client_id, $this->paypal->client_secret)
            ->post($this->apiUrl . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);
    }

    public function shouldRefreshAccessToken(): bool
    {
        return $this->paypal->access_token_expires_at &&
            $this->paypal->access_token_expires_at->diffInMinutes(now()) < 5;
    }

    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    public function refreshAccessToken(): void
    {
        $response = $this->getOAuthTokenResponse();

        if ($response->successful()) {
            $updatePaypalAction = new PaypalUpdateAction($this->paypal, [
                'access_token' => $response->json()['access_token'],
                'access_token_expires_at' => now()->addSeconds($response->json()['expires_in'])
            ], TriggerEnum::App);

            $updatePaypalAction->trigger();
        } else {
            throw new Exception($response->reason(), $response->status());
        }
    }

    protected function makeAmount(int $amount, string $currencyCode): array
    {
        return [
            'currency_code' => $currencyCode,
            'value' => $this->makeAmountValue($amount)
        ];
    }

    protected function makeAmountValue(int $amount): string
    {
        return number_format($amount / 100.0, '2', '.');
    }
}
