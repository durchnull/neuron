<?php

namespace App\Services\Integration\PaymentProvider;

use App\Contracts\Integration\PaymentProvider\MollieServiceContract;
use App\Enums\Payment\PaymentMethodEnum;
use App\Enums\Product\ProductTypeEnum;
use App\Exceptions\PaymentResourceException;
use App\Facades\Vat;
use App\Models\Engine\Address;
use App\Models\Engine\Order;
use App\Models\Engine\Item;
use App\Models\Engine\Product;
use App\Models\Integration\Integration;
use App\Models\Integration\PaymentProvider\Mollie;
use App\Models\Engine\Shipping;
use App\Services\Integration\PaymentProvider\Resources\MollieOrderResource;
use App\Services\Integration\PaymentProvider\Resources\Resource;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\OrderCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Types\PaymentMethod;

class MollieService implements MollieServiceContract
{
    // <editor-fold desc="Header">

    protected MollieApiClient $client;

    protected string $locale;

    protected string $currencyCode;

    /**
     * @throws ApiException
     */
    public function __construct(
        protected Mollie $mollie
    ) {
        $this->client = (new MollieApiClient())->setApiKey(
            $mollie->api_key
        );

        $this->locale = $this->mollie->salesChannel->locale;
        $this->currencyCode = $this->mollie->salesChannel->currency_code;
    }

    public static function getClientVersion(): string
    {
        return MollieApiClient::CLIENT_VERSION;
    }

    /**
     * @throws ApiException
     */
    public function test(): bool
    {
        $orders = $this->client->orders->page(null, 1);

        return $orders instanceof OrderCollection;
    }

    // </editor-fold>

    // <editor-fold desc="MollieServiceContract">

    /**
     * @param  Order  $order
     * @param  string  $webhookId
     * @param  array  $resourceData
     * @return Resource
     * @throws PaymentResourceException
     * @throws Exception
     */
    public function createResource(Order $order, string $webhookId, array $resourceData = []): Resource
    {
        try {
            return new MollieOrderResource(
                $this->client->orders->create(
                    $this->mapOrder(
                        $order,
                        $this->getWebhookUrl($order->id, $webhookId),
                        $resourceData,
                    ),
                    ['embed' => 'payments']
                )
            );
        } catch (ApiException $exception) {
            Log::error('Mollie order [' . $exception->getCode() . '] ' . $exception->getMessage());

            throw new PaymentResourceException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @throws ApiException
     */
    public function updateResource(string $id, Order $order, array $resourceData = []): Resource
    {
        // @todo update order
        // @todo update order-lines
        return $this->getResource($id);
    }

    /**
     * @throws Exception
     */
    public function placeResource(string $id, Order $order, array $resourceData = []): Resource
    {
        throw new Exception('Resource is placed on creation');
    }

    /**
     * @param  string  $id
     * @return Resource
     * @throws ApiException
     */
    public function getResource(string $id): Resource
    {
        return new MollieOrderResource(
            $this->client->orders->get($id, ['embed' => 'payments'])
        );
    }

    /**
     * @param  Order  $order
     * @param  string  $webhookUrl
     * @param  array  $resourceData
     * @return array
     * @throws Exception
     */
    public function mapOrder(Order $order, string $webhookUrl, array $resourceData = []): array
    {
        return [
            'amount' => $this->makeAmount($order->amount),
            'method' => $this->makeMethod($order->payment->method),
            'orderNumber' => $order->order_number,
            'locale' => $this->locale,
            'lines' => $this->makeLines($order),
            'billingAddress' => $this->makeAddress($order->billingAddress, $order->customer->email),
            'shippingAddress' => $this->makeAddress($order->shippingAddress, $order->customer->email),
            'redirectUrl' => $this->makeRedirectUrl($order),
            'cancelUrl' => $this->makeCancelUrl(),
            'webhookUrl' => $webhookUrl,
            'payment' => $this->makePaymentData($order, $resourceData),
            'metadata' => $this->makeMetadata($order)
        ];
    }

    /**
     * @param  string  $orderId
     * @param  string  $webhookId
     * @return string
     */
    public function getWebhookUrl(string $orderId, string $webhookId): string
    {
        $webhookUrl = route('integration.mollie.transaction', [
            'orderId' => $orderId,
            'webhookId' => $webhookId,
        ]);

        if (App::environment(['local', 'ddev']) && ($ngrokDomain = env('NGROK_DOMAIN'))) {
            $webhookUrl = str_replace(url('/'), 'https://' . $ngrokDomain, $webhookUrl);
        }

        return $webhookUrl;
    }

    /**
     * @return array
     */
    public static function getAllowedMethods(): array
    {
        return [
            PaymentMethodEnum::Applepay,
            PaymentMethodEnum::Creditcard,
            PaymentMethodEnum::Giropay,
            PaymentMethodEnum::KlarnaPayLater,
            PaymentMethodEnum::Paypal,
            PaymentMethodEnum::Prepayment,
            PaymentMethodEnum::Sofort,
        ];
    }


    /**
     * @return Integration
     */
    public function getIntegration(): Integration
    {
        return $this->mollie;
    }

    /**
     * @param  string  $id
     * @return Resource
     * @throws ApiException
     */
    public function refundResource(string $id): Resource
    {
        $resource = $this->getResource($id);

        /** @var \Mollie\Api\Resources\Order $order */
        $order = $resource->getResource();

        /** @var Payment $payment */
        foreach ($order->payments() as $payment) {
            // @todo refund data
            $payment->refund([]);
        }

        return $resource;
    }

    // </editor-fold>

    // <editor-fold desc="Maker">

    /**
     * @param  Order  $order
     * @return string
     */
    protected function makeRedirectUrl(Order $order): string
    {
        $redirectUrl = str_replace('orderId', $order->id, $order->salesChannel->order_summary_url);
        $redirectUrl = str_replace('orderNumber', $order->order_number, $redirectUrl);

        return $redirectUrl;
    }

    /**
     * @return string
     */
    protected function makeCancelUrl(): string
    {
        // @todo
        return $this->mollie->salesChannel->domains[0];
    }

    /**
     * @param  int  $amount
     * @return string
     */
    protected function makeTwoDecimalsFloat(int $amount): string
    {
        return number_format(
            (float)$amount / 100.0,
            2,
            '.',
            ''
        );
    }

    /**
     * @param  int  $amount
     * @return array
     */
    protected function makeAmount(int $amount): array
    {
        return [
            'currency' => $this->currencyCode,
            'value' => $this->makeTwoDecimalsFloat($amount)
        ];
    }

    /**
     * @param  int  $lineTotalAmount
     * @param  int  $vatRate
     * @return array
     */
    protected function makeVatAmount(int $lineTotalAmount, int $vatRate): array
    {
        $vatRate = $vatRate / 100.0;
        $lineTotalAmount = $lineTotalAmount / 100.0;

        $vatRateFactor = $vatRate / (100.0 + $vatRate);
        $vatAmount = $lineTotalAmount * $vatRateFactor;

        return [
            'currency' => $this->currencyCode,
            'value' => number_format(
                $vatAmount,
                2,
                '.',
                ''
            )
        ];
    }

    /**
     * @param  PaymentMethodEnum  $method
     * @return string
     * @throws Exception
     */
    protected function makeMethod(PaymentMethodEnum $method): string
    {
        return match ($method) {
            PaymentMethodEnum::Applepay => PaymentMethod::APPLEPAY,
            PaymentMethodEnum::Creditcard => PaymentMethod::CREDITCARD,
            PaymentMethodEnum::Giropay => PaymentMethod::GIROPAY,
            PaymentMethodEnum::KlarnaPayLater => PaymentMethod::KLARNA_PAY_LATER,
            PaymentMethodEnum::Paypal => PaymentMethod::PAYPAL,
            PaymentMethodEnum::Sofort => PaymentMethod::SOFORT,
            default => throw new Exception('Method not supported by Mollie'),
        };
    }

    /**
     * @param  Order  $order
     * @return array
     */
    protected function makeLines(Order $order): array
    {
        return array_merge(
            $this->makeItemLines($order),
            $this->makeShippingLines($order),
        );
    }

    /**
     * @param  Order  $order
     * @return array
     */
    protected function makeItemLines(Order $order): array
    {
        // @todo [optimization]
        $order->items->each(fn(Item $item) => $item->load('product'));

        return $order->items->map(function(Item $item) use ($order) {
            $vat = Vat::get(
                Product::class,
                $item->product_id,
                $order->shipping->country_code
            );

            $vatRate = optional($vat)->rate ?? 0;

            return [
                'name' => $item->product->name,
                'quantity' => $item->quantity,
                'unitPrice' => $this->makeAmount($item->unit_amount),
                'totalAmount' => $this->makeAmount($item->total_amount - $item->discount_amount),
                'discountAmount' => $this->makeAmount($item->discount_amount),
                'vatRate' => $this->makeTwoDecimalsFloat($vatRate),
                'vatAmount' => $this->makeVatAmount($item->total_amount - $item->discount_amount, $vatRate),
                'type' => $this->makeLineType($item),
                'sku' => $item->product->sku,
                'imageUrl' => $item->product->image_url,
                'productUrl' => $item->product->url
            ];
        })->toArray();
    }

    /**
     * @param  Order  $order
     * @return array[]
     */
    protected function makeShippingLines(Order $order): array
    {
        // @todo [implementation] Shipping split by vat rates

        $vat = Vat::get(
            Shipping::class,
            $order->shipping_id,
            $order->shipping->country_code
        );

        $vatRate = optional($vat)->rate ?? 0;

        return [
            [
                'type' => 'shipping_fee',
                'name' => $order->shipping->name,
                'quantity' => 1,
                'unitPrice' => $this->makeAmount($order->shipping_amount),
                'totalAmount' => $this->makeAmount($order->shipping_amount - $order->shipping_discount_amount),
                'discountAmount' => $this->makeAmount($order->shipping_discount_amount),
                'vatRate' => $this->makeTwoDecimalsFloat($vatRate),
                'vatAmount' => $this->makeVatAmount($order->shipping_amount - $order->shipping_discount_amount, $vatRate),
            ]
        ];
    }

    /**
     * physical, discount, digital, shipping_fee, store_credit, gift_card, surcharge
     *
     * @param  Item  $item
     * @return string
     * @throws Exception
     */
    protected function makeLineType(Item $item): string
    {
        return match ($item->product->type) {
            ProductTypeEnum::Product, ProductTypeEnum::Bundle => 'physical',
            default => throw new Exception('Product type not implemented')
        };
    }

    /**
     * @param  Address  $address
     * @param  string  $email
     * @return array
     */
    protected function makeAddress(Address $address, string $email): array
    {
        return [
            'givenName' => $address->first_name,
            'familyName' => $address->last_name,
            'streetAdditional' => $address->additional,
            'streetAndNumber' => implode(' ', [
                $address->street,
                $address->number,
            ]),
            'postalCode' => $address->postal_code,
            'city' => $address->city,
            'country' => $address->country_code,
            'email' => $email,
        ];
    }

    /**
     * @param  Order  $order
     * @param  array  $resourceData
     * @return array
     */
    protected function makePaymentData(Order $order, array $resourceData = []): array
    {
        $paymentData = [];

        switch ($order->payment->method) {
            case PaymentMethod::CREDITCARD:
                $paymentData['cardToken'] = $resourceData['card_token'];
                break;
        }

        return $paymentData;
    }

    /**
     * @param  Order  $order
     * @return array
     */
    protected function makeMetadata(Order $order): array
    {
        return [
            'order_id' => $order->id,
            'sales_channel_name' => $order->salesChannel->name
        ];
    }

    // </editor-fold>
}
