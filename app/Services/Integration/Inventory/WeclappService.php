<?php

namespace App\Services\Integration\Inventory;

use App\Actions\Engine\Product\ProductCreateAction;
use App\Actions\Engine\Product\ProductUpdateAction;
use App\Contracts\Integration\Inventory\WeclappServiceContract;
use App\Enums\Integration\IntegrationResourceStatusEnum;
use App\Enums\Order\OrderStatusEnum;
use App\Enums\Product\ProductTypeEnum;
use App\Enums\TriggerEnum;
use App\Facades\Stock;
use App\Models\Engine\Address;
use App\Models\Engine\Item;
use App\Models\Engine\Order;
use App\Models\Engine\Product;
use App\Models\Integration\Inventory\Weclapp;
use App\Models\Integration\OrderIntegration;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * @link https://www.weclapp.com/api/
 */
class WeclappService implements WeclappServiceContract
{
    // <editor-fold desc="Header">

    protected string $apiUrl;

    public function __construct(
        protected Weclapp $weclapp
    ) {
        $this->apiUrl = $this->weclapp->url . '/webapp/api/v1/';
    }

    // </editor-fold>

    // <editor-fold desc="IntegrationServiceContract">

    public static function getClientVersion(): string
    {
        return '23.03.1';
    }

    public function test(): bool
    {
        return $this->get('meta/resources')->ok();
    }


    // </editor-fold>

    // <editor-fold desc="ReceiveInventory">

    /**
     * @throws Exception
     */
    public function receiveInventory(): void
    {
        Log::channel('integration')->info('receiveInventory');

        if (!$this->weclapp->article_category_id) {
            throw new Exception('Set article category id');
        }

        if (!$this->weclapp->distribution_channel) {
            throw new Exception('Set distribution channel');
        }

        /** @var array $articles */
        $articles = $this->get('article', [
            //'articleCategoryId-eq' => $this->weclapp->article_category_id,
        ])->json()['result'];

        /** @var array $warehouseStock */
        $warehouseStock = $this->get('warehouseStock', [
            //'articleId-in' => implode('|', array_column($articles, 'id')) // @todo
        ])->json()['result'];

        foreach ($articles as $article) {
            if (!$this->shouldReceiveProduct($article)) {
                continue;
            }

            /** @var Product|null $product */
            $product = Product::with('vats')
                ->where('sales_channel_id', $this->weclapp->sales_channel_id)
                ->where('inventory_id', $article['id'])
                ->first();

            $attributes = $this->mapWeclappArticle($article);

            if (empty($article['articlePrices'])) {
                $attributes['enabled'] = false;
                $attributes['net_price'] = 0;
                $attributes['gross_price'] = 0;
            } else {
                $currentPrice = $article['articlePrices'][0]; // @todo check all
                $attributes['net_price'] = (int)($currentPrice['price'] * 100); // @todo parse
                $attributes['gross_price'] = (int)($currentPrice['price'] * 100); // @todo parse
            }

            // @todo vats
            if ($product) {
                $action = new ProductUpdateAction(
                    $product,
                    $attributes,
                    TriggerEnum::App
                );
            } else {
                $action = new ProductCreateAction(
                    new Product(),
                    $attributes,
                    TriggerEnum::App
                );
            }

            try {
                $action->trigger();
                $product = $action->target();

                $stock = Stock::get($product->id);
                $queue = Stock::get($product->id, true);

                $articleStocks = array_filter(
                    $warehouseStock,
                    fn(array $stock) => $stock['articleId'] === $article['id']
                );
                $weclappStock = count($articleStocks) === 1 ? $articleStocks[0]['quantity'] : 0;
                $stockDiff = $weclappStock - $stock + $queue;

                if ($stockDiff > 0) {
                    Stock::add($product->id, $stockDiff);
                } elseif ($stockDiff < 0) {
                    Stock::remove($product->id, $stockDiff);
                }
            } catch (Exception $exception) {
                Log::channel('integration')->error($exception->getMessage());
            }
        }

        Log::channel('integration')->info('receiveInventory finished');
    }

    protected function shouldReceiveProduct(array $article): bool
    {
        return true;
    }

    protected function mapWeclappArticle(array $article): array
    {
        return [
            'sales_channel_id' => $this->weclapp->sales_channel_id,
            'inventoryable_type' => get_class($this->weclapp),
            'inventoryable_id' => $this->weclapp->id,
            'inventory_id' => $article['id'],
            'type' => ProductTypeEnum::Product->value, // @todo bundle
            'enabled' => true,
            'sku' => trim($article['articleNumber']),
            'ean' => null,
            'name' => trim($article['name']),
            'configuration' => null,
            'url' => null,
            'image_url' => null,
        ];
    }

    // </editor-fold>

    // <editor-fold desc="DistributeOrder">

    public function distributeOrder(Order $order): void
    {
        try {
            $salesOrderResponse = $this->post('salesOrder', $this->mapOrder($order));

            $resourceId = $salesOrderResponse->json()['result']['id'];
            $orderIntegrationStatus = IntegrationResourceStatusEnum::Distributed;
        } catch (Exception $exception) {
            $resourceId = '';
            $orderIntegrationStatus = IntegrationResourceStatusEnum::DistributedFailed;
            Log::channel('integration')->info($exception->getMessage());
        }

        $orderIntegration = OrderIntegration::updateOrCreate(
            [
                'order_id' => $order->id,
                'integration_id' => $this->weclapp->id,
                'integration_type' => get_class($this->weclapp),
            ],
            [
                'resource_id' => $resourceId,
                'status' => $orderIntegrationStatus,
            ]
        );
    }

    // </editor-fold>

    // <editor-fold desc="API">

    protected function getBaseRequest(): PendingRequest
    {
        return Http::withHeaders([
            'AuthenticationToken' => $this->weclapp->api_token
        ]);
    }

    protected function get(string $path, array $parameters = []): Response
    {
        $url = $this->apiUrl . $path;

        if (!empty($parameters)) {
            $url .= '/' . http_build_query($parameters);
        }

        return $this->getBaseRequest()->get($url);
    }

    protected function post(string $path, array $parameters = []): Response
    {
        return $this->getBaseRequest()->post($this->apiUrl . $path, $parameters);
    }

    // </editor-fold>

    // <editor-fold desc="Utility">

    protected function mapOrder(Order $order): array
    {
        return [
            'id' => $order->id,
            'version' => $order->version,
            'createdDate' => $order->created_at,
            'deliveryAddress' => $this->mapAddress($order->shippingAddress),
            //'grossAmount' => 'string',
            //'grossAmountInCompanyCurrency' => 'string',
            'invoiceAddress' => $this->mapAddress($order->billingAddress),
            'invoiced' => true,
            'lastModifiedDate' => $order->updated_at,
            'netAmount' => $order->amount,
            'netAmountInCompanyCurrency' => $order->salesChannel->currency_code,
            'orderDate' => $order->ordered_at,
            'orderItems' => $this->makeOrderItems($order),
            'orderNumber' => $order->order_number,
            'paid' => true,
            //'paymentMethodId' => 'string', // @todo
            //'paymentMethodName' => 'string',
            //'salesChannel' => 'string',
            //'shipmentMethodId' => 'string', @todo
            //'shipmentMethodName' => 'string',
            'shippingCostItems' => $this->makeShippingCostItems($order),
            'status' => $this->makeStatus($order->status),
            'warehouseId' => $this->weclapp->warehouse_id,
        ];
    }

    protected function mapAddress(Address $address): array
    {
        return [
            'city' => $address->city,
            'company' => $address->company,
            'company2' => null,
            'countryCode' => $address->country_code,
            'firstName' => $address->first_name,
            'lastName' => $address->last_name,
            'phoneNumber' => 'string',
            //'postOfficeBoxCity' => 'string',
            //'postOfficeBoxNumber' => 'string',
            //'postOfficeBoxZipCode' => 'string',
            //'salutation' => 'COMPANY',
            //'state' => 'string',
            'street1' => $address->street,
            'street2' => $address->additional,
            //'title' => 'string',
            //'titleId' => 'string',
            'zipcode' => $address->postal_code
        ];
    }

    protected function makeOrderItems(Order $order): array
    {
        return $order->items->map(fn(Item $item) => [
            'id' => $item->id,
            'version' => $order->version,
            'articleId' => $item->product_id,
            'articleNumber' => $item->product->sku,
            'availability' => 'COMPLETELY_AVAILABLE',
            'availabilityForAllWarehouses' => 'COMPLETELY_AVAILABLE',
            'createdDate' => $item->created_at,
            'discountPercentage' => $item->discount_amount / $item->total_amount * 100,
            'lastModifiedDate' => $item->updated_at,
            'netAmount' => $item->total_amount,
            'positionNumber' => $item->position,
            'quantity' => $item->quantity,
            'title' => $item->product->name,
            'unitPrice' => $item->unit_amount,
            'unitPriceInCompanyCurrency' => $order->salesChannel->currency_code
        ])->toArray();
    }

    protected function makeShippingCostItems(Order $order): array
    {
        return [
            [
                'id' => 'string',
                'version' => 'string',
                'articleId' => 'string',
                'articleNumber' => 'string',
                'createdDate' => 0,
                'grossAmount' => 'string',
                'grossAmountInCompanyCurrency' => 'string',
                'lastModifiedDate' => 0,
                'manualUnitCost' => true,
                'manualUnitPrice' => true,
                'netAmount' => 'string',
                'netAmountInCompanyCurrency' => 'string',
                'taxId' => 'string',
                'taxName' => 'string',
                'unitCost' => 'string',
                'unitCostInCompanyCurrency' => 'string',
                'unitPrice' => 'string',
                'unitPriceInCompanyCurrency' => 'string'
            ]
        ];
    }

    protected function makeStatus(OrderStatusEnum $status): string
    {
        return match ($status) {
            default => 'ORDERED'
        };
    }
}
