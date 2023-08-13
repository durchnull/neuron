<?php

namespace App\Services\Integration\Inventory;

use App\Actions\Engine\Product\ProductCreateAction;
use App\Actions\Engine\Product\ProductUpdateAction;
use App\Contracts\Integration\Inventory\BillbeeServiceContract;
use App\Enums\Integration\IntegrationResourceStatusEnum;
use App\Enums\Order\OrderStatusEnum;
use App\Enums\Payment\PaymentMethodEnum;
use App\Enums\Product\ProductTypeEnum;
use App\Enums\TriggerEnum;
use App\Facades\Stock;
use App\Models\Engine\Address;
use App\Models\Engine\Item;
use App\Models\Engine\Order;
use App\Models\Integration\Inventory\Billbee;
use App\Models\Integration\OrderIntegration;
use BillbeeDe\BillbeeAPI\Client;
use BillbeeDe\BillbeeAPI\Exception\QuotaExceededException;
use BillbeeDe\BillbeeAPI\Model\Customer;
use BillbeeDe\BillbeeAPI\Model\OrderItem;
use BillbeeDe\BillbeeAPI\Model\Product;
use BillbeeDe\BillbeeAPI\Model\TranslatableText;
use BillbeeDe\BillbeeAPI\Type\OrderState;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * @link https://github.com/billbeeio/billbee-php-sdk
 */
class BillbeeService implements BillbeeServiceContract
{
    // <editor-fold desc="Header">

    protected Client $client;

    protected string $languageCode;

    /**
     * @throws Exception
     */
    public function __construct(protected Billbee $billbee)
    {
        // @todo sandbox/production
        $clientClass = BillbeeSandboxClient::class;

        $this->client = new $clientClass($billbee->user, $billbee->api_password, $billbee->api_key);

        $this->languageCode = Str::after($this->billbee->salesChannel->locale, '_');
    }

    // </editor-fold>

    // <editor-fold desc="IntegrationServiceContract">

    public static function getClientVersion(): string
    {
        return '2.2.1';
    }

    public function test(): bool
    {
        try {
            $response = $this->client->products()->getProducts(1, 1);
            return true;
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }
    }

    // </editor-fold>

    // <editor-fold desc="ReceiveInventory">

    protected function shouldReceiveProduct(Product $product): bool
    {
        // $product->getSources() ?

        return true;
    }

    /**
     * @throws Exception
     */
    public function receiveInventory(): void
    {
        $pageSize = 40;
        $page = 1;

        $loadNextPage = true;

        while ($loadNextPage) {
            Log::channel('integration')->info('Page ' . $page);

            try {
                $productsResponse = $this->client->products()->getProducts($page, $pageSize);
                $billbeeProducts = $productsResponse->getData();
            } catch (Exception $exception) {
                Log::channel('integration')->info($exception->getMessage());
                $billbeeProducts = [];
            }

            Log::channel('integration')->info(count($billbeeProducts) . ' products');

            /** @var Product $product */
            foreach ($billbeeProducts as $billbeeProduct) {
                if (!$this->shouldReceiveProduct($billbeeProduct)) {
                    continue;
                }
                /** @var \App\Models\Engine\Product|null $product */
                $product = \App\Models\Engine\Product::with('vats')
                    ->where('sales_channel_id', $this->billbee->sales_channel_id)
                    ->where('inventory_id', $billbeeProduct->getId())
                    ->first();

                $attributes = $this->mapBillbeeProduct($billbeeProduct);

                // @todo vats
                if ($product) {
                    $action = new ProductUpdateAction(
                        $product,
                        $attributes,
                        TriggerEnum::App
                    );
                } else {
                    $action = new ProductCreateAction(
                        new \App\Models\Engine\Product(),
                        $attributes,
                        TriggerEnum::App
                    );
                }

                try {
                    $action->trigger();
                    $product = $action->target();

                    $stock = Stock::get($product->id);
                    $queue = Stock::get($product->id, true);

                    $billbeeStock = (int)$billbeeProduct->getStockCurrent();
                    $stockDiff = $billbeeStock - $stock + $queue;

                    if ($stockDiff > 0) {
                        Stock::add($product->id, $stockDiff);
                    } elseif ($stockDiff < 0) {
                        Stock::remove($product->id, $stockDiff);
                    }
                } catch (Exception $exception) {
                    Log::channel('integration')->error($exception->getMessage());
                }
            }

            $loadNextPage = count($billbeeProducts) === $pageSize;

            if ($loadNextPage) {
                $page++;
            }
        }

        Log::channel('integration')->info('receiveInventory finish');
    }

    // </editor-fold>

    // <editor-fold desc="DistributeOrder">


    /**
     * @throws QuotaExceededException
     */
    public function distributeOrder(Order $order): void
    {
        try {
            $response = $this->client->orders()->createOrder(
                $this->mapOrder($order),
                $this->billbee->shop_id
            );

            /** @var \BillbeeDe\BillbeeAPI\Model\Order $billbeeOrder */
            $billbeeOrder = $response->getData();
            $resourceId = $billbeeOrder->getId();
            $orderIntegrationStatus = IntegrationResourceStatusEnum::Distributed;
        } catch (Exception $exception) {
            $resourceId = '';
            $orderIntegrationStatus = IntegrationResourceStatusEnum::DistributedFailed;
            Log::channel('integration')->info($exception->getMessage());
        }

        $orderIntegration = OrderIntegration::updateOrCreate(
            [
                'order_id' => $order->id,
                'integration_id' => $this->billbee->id,
                'integration_type' => get_class($this->billbee),
            ],
            [
                'resource_id' => $resourceId,
                'status' => $orderIntegrationStatus,
            ]
        );
    }

    // </editor-fold>

    // <editor-fold desc="Utility">

    protected function getTextFromTranslateableText(array $translatableTexts, string $languageCode): string
    {
        $translatableText = collect($translatableTexts)->first(
            fn(TranslatableText $translatableText) => $translatableText->getLanguageCode() === $languageCode
        );

        return optional($translatableText)->getText() ?? '';
    }

    protected function mapBillbeeProduct(Product $product): array
    {
        return [
            'sales_channel_id' => $this->billbee->sales_channel_id,
            'inventoryable_type' => get_class($this->billbee),
            'inventoryable_id' => $this->billbee->id,
            'inventory_id' => (string)$product->getId(),
            'type' => match ($product->getType()) {
                Product::TYPE_PRODUCT => ProductTypeEnum::Product->value,
                Product::TYPE_BUNDLE => ProductTypeEnum::Bundle->value,
            },
            'enabled' => true,
            'sku' => trim($product->getSku()),
            'ean' => trim($product->getEan()),
            'name' => $this->getTextFromTranslateableText($product->getTitle(), $this->languageCode),
            'net_price' => $this->makeAmount($product->getPrice()),
            'gross_price' => $product->getCostPrice()
                ? $this->makeAmount($product->getCostPrice())
                : 0,
            'configuration' => null,
            'url' => null,
            'image_url' => null,
        ];
    }

    protected function makeAmount(float $number): int
    {
        return (int)($number * 100);
    }

    public function mapOrder(Order $order): \BillbeeDe\BillbeeAPI\Model\Order
    {
        return (new \BillbeeDe\BillbeeAPI\Model\Order())
            ->setExternalId($order->id)
            ->setOrderNumber($order->order_number)
            ->setState($this->makeState($order->status))
            ->setVatMode(\BillbeeDe\BillbeeAPI\Model\Order::VAT_MODE_DEFAULT)
            ->setCreatedAt($order->ordered_at)
            // ->setConfirmedAt()
            // ->setPayedAt()
            ->setShippingAddress($this->makeAddress($order->shippingAddress, $order->customer->email))
            ->setPaymentMethod($this->makePaymentMethod($order->payment->method))
            ->setShippingCost($this->makeCost($order->shipping_amount - $order->shipping_discount_amount))
            ->setTotalCost($this->makeCost($order->amount))
            ->setOrderItems($this->makeOrderItems($order))
            ->setCurrency($this->billbee->salesChannel->currency_code)
            //->setSeller($this->makeSeller())
            ->setPaidAmount($this->makeCost($order->amount))
            //->setPaymentTransactionId()
            //->setVatId()
            ->setCustomerNumber($order->customer->id)
            ->setCustomer($this->makeCustomer($order->customer));
    }

    protected function makeCustomer(\App\Models\Engine\Customer $customer): Customer
    {
        return (new Customer())
            ->setName($customer->full_name);
    }

    /**
     * @throws Exception
     */
    protected function makeState(OrderStatusEnum $orderStatus): int
    {
        return match ($orderStatus) {
            OrderStatusEnum::Open, OrderStatusEnum::Placing => throw new Exception('Order flow mismatch'),
            OrderStatusEnum::Accepted => OrderState::ORDERED, // @todo
            OrderStatusEnum::Confirmed => OrderState::CONFIRMED,
            OrderStatusEnum::Shipped => OrderState::SHIPPED,
            OrderStatusEnum::Refunded => OrderState::RECLAMATION,
            OrderStatusEnum::Canceled => OrderState::CANCELED
        };
    }

    protected function makeAddress(Address $address, string $email): \BillbeeDe\BillbeeAPI\Model\Address
    {
        return (new \BillbeeDe\BillbeeAPI\Model\Address());
    }

    protected function makePaymentMethod(PaymentMethodEnum $paymentMethod): int
    {
        return match ($paymentMethod) {
            PaymentMethodEnum::Prepayment => throw new \Exception('To be implemented'),
            PaymentMethodEnum::Applepay => throw new \Exception('To be implemented'),
            PaymentMethodEnum::Creditcard => throw new \Exception('To be implemented'),
            PaymentMethodEnum::Free => throw new \Exception('To be implemented'),
            PaymentMethodEnum::Giropay => throw new \Exception('To be implemented'),
            PaymentMethodEnum::KlarnaPayLater => throw new \Exception('To be implemented'),
            PaymentMethodEnum::Paypal => throw new \Exception('To be implemented'),
            PaymentMethodEnum::Proxy => throw new \Exception('To be implemented'),
            PaymentMethodEnum::Sofort => throw new \Exception('To be implemented')
        };
    }

    protected function makeCost(int $amount): float
    {
        return $amount / 100.0;
    }

    protected function makeOrderItems(Order $order): array
    {
        return array_map(
            fn(Item $item) => $this->makeOrderItem($item),
            $order->items
        );
    }

    protected function makeOrderItem(Item $item): OrderItem
    {
        return (new OrderItem())
            ->setQuantity($item->quantity);
    }

    // </editor-fold>
}
