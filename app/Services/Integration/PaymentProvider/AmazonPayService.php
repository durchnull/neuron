<?php

namespace App\Services\Integration\PaymentProvider;

use Amazon\Pay\API\Client;
use App\Contracts\Integration\PaymentProvider\AmazonPayServiceContract;
use App\Enums\Payment\PaymentMethodEnum;
use App\Exceptions\PaymentResourceException;
use App\Facades\Shipping;
use App\Models\Engine\Order;
use App\Models\Integration\Integration;
use App\Models\Integration\PaymentProvider\AmazonPay;
use App\Services\Integration\PaymentProvider\Resources\AmazonPayCheckoutSessionResource;
use App\Services\Integration\PaymentProvider\Resources\Resource;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * @link https://github.com/amzn/amazon-pay-api-sdk-php
 */
class AmazonPayService implements AmazonPayServiceContract
{
    // <editor-fold desc="Header">

    protected Client $client;

    public const Algorithm = 'AMZN-PAY-RSASSA-PSS-V2';

    protected string $paymentIntent;

    /**
     * @throws Exception
     */
    public function __construct(
        protected AmazonPay $amazonPay
    ) {
        $this->client = new Client([
            'public_key_id' => $this->amazonPay->public_key_id,
            'private_key' => $this->amazonPay->private_key,
            'region' => $this->amazonPay->region,
            'sandbox' => $this->amazonPay->sandbox,
            'algorithm' => self::getAlgorithm(),
            // @todo
            // 'integrator_id' => 'AXXXXXXXXXXXXX',  // (optional) Solution Provider Platform Id in Amz UID Format
            // 'integrator_version' => '1.2.3', // (optional) Solution Provider Plugin Version in Semantic Versioning Format
            // 'platform_version' => '0.0.4' // (optional) Solution Provider Platform Version in Semantic Versioning Format
        ]);

        $this->paymentIntent = 'Authorize';

    }

    // </editor-fold>

    // <editor-fold desc="IntegrationServiceContract">

    public static function getClientVersion(): string
    {
        return Client::SDK_VERSION;
    }

    /**
     * @return bool
     * @throws PaymentResourceException
     */
    public function test(): bool
    {
        $checkoutSession = $this->createCheckoutSession([
            $this->makeCheckoutSessionPayload(Order::factory()->make()) // @todo
        ]);

        return $checkoutSession['status'] === 201;
    }

    // </editor-fold>

    // <editor-fold desc="PaymentProviderServiceContract">

    /**
     * @param  Order  $order
     * @param  string  $webhookId
     * @param  array  $resourceData
     * @return Resource
     * @throws Exception
     */
    public function createResource(Order $order, string $webhookId, array $resourceData = []): Resource
    {
        return new AmazonPayCheckoutSessionResource(
            $this->createCheckoutSession(
                $this->makeCheckoutSessionPayload($order)
            )
        );
    }

    /**
     * @param  string  $id
     * @param  Order  $order
     * @param  array  $resourceData
     * @return Resource
     * @throws PaymentResourceException
     */
    public function updateResource(string $id, Order $order, array $resourceData = []): Resource
    {
        return new AmazonPayCheckoutSessionResource(
            $this->updateCheckoutSession(
                $id,
                $order->amount,
                $this->paymentIntent,
                $order->salesChannel->currency_code,
            )
        );
    }

    /**
     * @param  string  $id
     * @param  Order  $order
     * @param  array  $resourceData
     * @return Resource
     * @throws PaymentResourceException
     */
    public function placeResource(string $id, Order $order, array $resourceData = []): Resource
    {
        $resource = $this->completeCheckoutSession(
            $id,
            $order->amount,
            $order->salesChannel->currency_code,
        );

        return new AmazonPayCheckoutSessionResource($resource);
    }

    /**
     * @param  string  $id
     * @return Resource
     * @throws PaymentResourceException
     */
    public function getResource(string $id): Resource
    {
        Log::channel('payment')->info('getResource ' . $id);

        return new AmazonPayCheckoutSessionResource(
            $this->getCheckoutSession($id)
        );
    }

    public function mapOrder(Order $order, string $webhookUrl): array
    {
        return $this->makePaymentDetails(
            $order->amount,
            $order->salesChannel->currency_code,
            $this->paymentIntent,
            false,
        );
    }

    public function getWebhookUrl(string $orderId, string $webhookId): string
    {
        return ''; // @todo [implementation]
    }

    public static function getAllowedMethods(): array
    {
        return [
            PaymentMethodEnum::Proxy,
        ];
    }

    public function getIntegration(): Integration
    {
        return $this->amazonPay;
    }

    public function refundResource(string $id): Resource
    {
        /** @var AmazonPayCheckoutSessionResource $resource */
        $resource = $this->getResource($id);
        /** @var array $checkoutSession */
        $checkoutSession = $resource->getResource();

        $refund = $this->createRefund(
            $checkoutSession['chargeId'],
            $checkoutSession['paymentDetails']['chargeAmount'], // @todo totalOrderAmount?
            $checkoutSession['paymentDetails']['presentmentCurrency'], // @todo
            '' // @todo
        );

        // @todo return refund or reload resource?
        return $this->getResource($id);
    }

    // </editor-fold>

    // <editor-fold desc="AmazonPayServiceContract">

    public static function getAlgorithm(): string
    {
        return self::Algorithm;
    }

    public function makeCheckoutSessionPayload(Order $order): array
    {
        return [
            'webCheckoutDetails' => [
                'checkoutReviewReturnUrl' => $this->makeCheckoutReviewReturnUrl($order),
                'checkoutResultReturnUrl' => $this->makeCheckoutResultReturnUrl($order),
                //'checkoutMode' => 'ProcessOrder'
            ],
            'chargePermissionType' => 'OneTime',
            'paymentDetails' => [
                'paymentIntent' => $this->paymentIntent,
                'chargeAmount' => $this->makeAmount(
                    $order->amount,
                    $this->amazonPay->salesChannel->currency_code
                ),
                'presentmentCurrency' => $this->amazonPay->salesChannel->currency_code
            ],
            'storeId' => $this->amazonPay->store_id,
            'scopes' => [
                'name',
                'email',
                'phoneNumber',
                'billingAddress',
                'shippingAddress', // @todo ?
            ],
            'merchantMetadata' => $this->makeMerchantData(),
            'deliverySpecifications' => [
                'specialRestrictions' => [
                    'RestrictPOBoxes',
                    'RestrictPackstations' // @todo
                ],
                //'addressRestrictions' => [
                //    'type' => 'Allowed',
                //    'restrictions' => array_fill_keys(Shipping::getCountryCodes(), [])
                //]
            ]
        ];
    }

    // </editor-fold>

    // <editor-fold desc="Amazon Checkout v2">

    /**
     * @throws PaymentResourceException
     */
    protected function middleware(array $response): array
    {
        $_response = json_decode($response['response'], true);

        // @todo [debug]
        $log = $response;
        unset($log['headers']);
        unset($log['retries']);
        unset($log['duration']);
        $log['response'] = $this->filterNullValues(json_decode($log['response'], true));
        Log::channel('payment')->info(json_encode($log, JSON_PRETTY_PRINT));

        if (!($response['status'] >= 200 && $response['status'] < 300)) {
            throw new PaymentResourceException(
                "[{$_response['reasonCode']}] {$_response['message']}",
                $response['status']
            );
        }

        return $_response;
    }

    // @todo
    protected function filterNullValues(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->filterNullValues($value);
            }

            if ($array[$key] === null) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    // <editor-fold desc="Buyer">

    /**
     * @param  string  $buyerToken
     * @return array
     * @throws PaymentResourceException
     */
    protected function getBuyer(string $buyerToken): array
    {
        return $this->middleware(
            $this->client->getBuyer($buyerToken)
        );
    }

    // </editor-fold>

    // <editor-fold desc="Button">

    /**
     * @throws Exception
     */
    public function generateButtonSignature(array $payload): string
    {
        return $this->client->generateButtonSignature($payload);
    }

    // </editor-fold>

    // <editor-fold desc="CheckoutSession">

    /**
     * @param  array  $payload
     * @return array
     * @throws PaymentResourceException
     */
    protected function createCheckoutSession(array $payload): array
    {
        return $this->middleware(
            $this->client->createCheckoutSession($payload, $this->makeHeaders())
        );
    }

    /**
     * @param  string  $checkoutSessionId
     * @return array
     * @throws PaymentResourceException
     */
    protected function getCheckoutSession(string $checkoutSessionId): array
    {
        return $this->middleware(
            $this->client->getCheckoutSession($checkoutSessionId)
        );
    }

    /**
     * @param  string  $checkoutSessionId
     * @param  int  $amount
     * @param  string  $currencyCode
     * @return array
     * @throws PaymentResourceException
     */
    protected function updateCheckoutSession(
        string $checkoutSessionId,
        int $amount,
        string $paymentIntent,
        string $currencyCode
    ): array {
        return $this->middleware(
            $this->client->updateCheckoutSession($checkoutSessionId, [
                'paymentDetails' => $this->makePaymentDetails(
                    $amount,
                    $currencyCode,
                    $paymentIntent,
                    false
                ),
                'merchantMetadata' => $this->makeMerchantData()
            ])
        );
    }

    /**
     * @param  string  $checkoutSessionId
     * @param  int  $amount
     * @param  string  $currencyCode
     * @return array
     * @throws PaymentResourceException
     */
    protected function completeCheckoutSession(
        string $checkoutSessionId,
        int $amount,
        string $currencyCode
    ): array {
        return $this->middleware(
            $this->client->completeCheckoutSession($checkoutSessionId, [
                'chargeAmount' => $this->makeAmount($amount, $currencyCode)
            ])
        );
    }

    // </editor-fold>

    // <editor-fold desc="ChargePermission">

    /**
     * @param  string  $chargePermissionId
     * @return array
     * @throws PaymentResourceException
     */
    protected function getChargePermission(string $chargePermissionId): array
    {
        return $this->middleware(
            $this->client->getChargePermission($chargePermissionId)
        );
    }

    /**
     * @param  string  $chargePermissionId
     * @return array
     * @throws PaymentResourceException
     */
    protected function updateChargePermission(string $chargePermissionId): array
    {
        return $this->middleware(
            $this->client->updateChargePermission($chargePermissionId, [
                'merchantMetadata' => $this->makeMerchantData()
            ])
        );
    }

    /**
     * @param  string  $chargePermissionId
     * @param  string  $closureReason
     * @param  bool  $cancelPendingCharges
     * @return array
     * @throws PaymentResourceException
     */
    protected function closeChargePermission(
        string $chargePermissionId,
        string $closureReason,
        bool $cancelPendingCharges
    ): array {
        return $this->middleware(
            $this->client->closeChargePermission($chargePermissionId, [
                'closureReason' => $closureReason,
                'cancelPendingCharges' => $cancelPendingCharges
            ])
        );
    }

    // </editor-fold>

    // <editor-fold desc="Charge">

    /**
     * @param  string  $chargePermissionId
     * @param  int  $amount
     * @param  int  $currencyCode
     * @param  bool  $captureNow
     * @param  string  $softDescriptor
     * @param  bool  $canHandlePendingAuthorization
     * @return array
     * @throws PaymentResourceException
     */
    protected function createCharge(
        string $chargePermissionId,
        int $amount,
        int $currencyCode,
        bool $captureNow,
        string $softDescriptor,
        bool $canHandlePendingAuthorization
    ): array {
        return $this->middleware(
            $this->client->createCharge([
                'chargePermissionId' => $chargePermissionId,
                'chargeAmount' => $this->makeAmount($amount, $currencyCode),
                'captureNow' => $captureNow,
                'softDescriptor' => $softDescriptor,
                'canHandlePendingAuthorization' => $canHandlePendingAuthorization
            ], $this->makeHeaders())
        );
    }

    /**
     * @param  string  $chargeId
     * @return array
     * @throws PaymentResourceException
     */
    protected function getCharge(string $chargeId): array
    {
        return $this->middleware(
            $this->getCharge($chargeId)
        );
    }

    /**
     * @throws PaymentResourceException
     */
    protected function captureCharge(
        string $chargeId,
        int $amount,
        string $currencyCode,
        string $softDescriptor
    ): array {
        return $this->middleware(
            $this->client->captureCharge($chargeId, [
                'captureAmount' => $this->makeAmount($amount, $currencyCode),
                'softDescriptor' => $softDescriptor
            ], $this->makeHeaders())
        );
    }

    /**
     * @param  string  $chargeId
     * @param  string  $cancellationReason
     * @return array
     * @throws PaymentResourceException
     */
    protected function cancelCharge(
        string $chargeId,
        string $cancellationReason,
    ): array {
        return $this->middleware(
            $this->client->cancelCharge($chargeId, [
                'cancellationReason' => $cancellationReason
            ])
        );
    }

    // </editor-fold>

    // <editor-fold desc="Refund">

    /**
     * @param  string  $chargeId
     * @param  int  $amount
     * @param  string  $currencyCode
     * @param  string  $softDescriptor
     * @return array
     * @throws PaymentResourceException
     */
    protected function createRefund(
        string $chargeId,
        int $amount,
        string $currencyCode,
        string $softDescriptor,
    ): array {
        return $this->middleware(
            $this->client->createRefund([
                'chargeId' => $chargeId,
                'refundAmount' => $this->makeAmount($amount, $currencyCode),
                'softDescriptor' => $softDescriptor
            ], $this->makeHeaders())
        );
    }

    /**
     * @throws PaymentResourceException
     */
    protected function getRefund(string $refundId): array
    {
        return $this->middleware(
            $this->client->getRefund($refundId)
        );
    }

    // </editor-fold>

    // </editor-fold>

    // <editor-fold desc="Maker">

    /**
     * @param  Order  $order
     * @return string
     */
    protected function makeCheckoutReviewReturnUrl(Order $order): string
    {
        $checkoutReviewReturnUrl = str_replace('orderId', $order->id, $order->salesChannel->checkout_summary_url);
        $checkoutReviewReturnUrl = str_replace('orderNumber', $order->order_number, $checkoutReviewReturnUrl);

        return $checkoutReviewReturnUrl;
    }

    /**
     * @param  Order  $order
     * @return string
     */
    protected function makeCheckoutResultReturnUrl(Order $order): string
    {
        $checkoutResultReturnUrl = str_replace('orderId', $order->id, $order->salesChannel->order_summary_url);
        $checkoutResultReturnUrl = str_replace('orderNumber', $order->order_number, $checkoutResultReturnUrl);

        return $checkoutResultReturnUrl;
    }

    protected function makeHeaders(): array
    {
        return [
            'x-amz-pay-Idempotency-Key' => uniqid()
        ];
    }

    protected function makePaymentDetails(
        int $amount,
        string $currencyCode,
        string $paymentIntent,
        bool $canHandlePendingAuthorization,
    ): array {
        return [
            'paymentIntent' => $paymentIntent,
            'canHandlePendingAuthorization' => $canHandlePendingAuthorization,
            'chargeAmount' => $this->makeAmount($amount, $currencyCode)
        ];
    }

    protected function makeAmount(int $amount, string $currencyCode): array
    {
        return [
            'amount' => $this->formatAmount($amount),
            'currencyCode' => $currencyCode
        ];
    }

    protected function formatAmount(int $amount): string
    {
        return number_format($amount / 100.0, 2, '.');
    }

    protected function makeMerchantData(
        string $noteToBuyer = '',
        string $customInformation = null
    ): array {
        return array_filter([
            'merchantReferenceId' => $this->amazonPay->salesChannel->id,
            'merchantStoreName' => $this->amazonPay->salesChannel->name,
            'noteToBuyer' => $noteToBuyer,
            'customInformation' => $customInformation
        ]);
    }

    // </editor-fold>
}
