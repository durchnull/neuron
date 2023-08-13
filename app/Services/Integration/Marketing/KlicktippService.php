<?php

namespace App\Services\Integration\Marketing;

use App\Contracts\Integration\Marketing\KlicktippServiceContract;
use App\Models\Engine\Order;
use App\Models\Engine\Item;
use App\Models\Engine\Coupon;
use App\Models\Integration\Marketing\Klicktipp;
use Carbon\Carbon;
use Exception;

class KlicktippService implements KlicktippServiceContract
{
    // @todo [implementation]
    // /** @var KlicktippPartnerConnector $client */
    // protected KlicktippPartnerConnector $client;

    public function __construct(
        protected Klicktipp $klicktipp
    ) {
    }

    public static function getClientVersion(): string
    {
        return '3.3.1';
    }

    /**
     * @throws Exception
     */
    public function distributeOrder(Order $order): void
    {
        // @todo [implementation]
        $subscriber = $this->client->subscribe($order->customer->email);

        if (!$subscriber) {
            // OrderIntegration
            throw new Exception('Could not subscribe email');
        }

        $this->createNewProductTags($order);

        $updateResponse = $this->client->subscriber_update($subscriber->id, $this->mapOrder($order, $subscriber));

        if (! $updateResponse) {

        }

        $tagResponse = $this->client->tag($order->customer->email, $this->mapTags($order));

        if (! $updateResponse) {

        }
    }

    protected function createNewProductTags(Order $order): void
    {
        $order->items->filter(fn(Item $item) => !isset($this->klicktipp->tags_products[$item->product_id]))
            ->each(function (Item $item) {
                $tagName = $this->klicktipp->tag_prefix . $item->product->name;

                try {
                    $tagId = $this->client->tag_create($tagName);

                    if ($tagId) {
                        $this->klicktipp->tags_products[$item->product_id] = $tagId;
                        $this->klicktipp->saveQuietly();
                    }
                } catch (Exception $exception) {
                }
            });
    }

    protected function mapOrder(Order $order, object $subscriber): array
    {
        return [
            'fieldFirstName' => $order->billingAddress->first_name,
            'fieldLastName' => $order->billingAddress->last_name,
            'fieldStreet1' => implode(' ', [$order->billingAddress->street, $order->billingAddress->number]),
            'fieldCity' => $order->billingAddress->city,
            'fieldZip' => $order->billingAddress->postal_code,
            'fieldCountry' => $order->billingAddress->country_code,
            'fieldLeadValue' => (int)$subscriber->fieldLeadValue + $order->amount
        ];
    }

    public function mapTags(Order $order): array
    {
        return collect([
            $this->klicktipp->tags,
            $order->customer->new ? $this->klicktipp->tags_new_customer : [],
            $order->items->map(fn(Item $item) => $this->klicktipp->tags_products[$item->product_id] ?? null)
                ->filter()
                ->unique()
                ->toArray(),
            $order->coupons->map(fn(Coupon $coupon) => $this->klicktipp->tags_coupons[$coupon->code] ?? null)
                ->filter()
                ->unique()
                ->toArray(),
            array_map(
                fn(array $periodTag) => $periodTag['tag'],
                array_filter(
                    $this->klicktipp->tags_periods,
                    fn(array $periodTag) => now()->isAfter(Carbon::createFromFormat('Y-m-d H:i:s', $periodTag['from']))
                        && now()->isBefore(Carbon::createFromFormat('Y-m-d H:i:s', $periodTag['to']))
                )
            )
        ])
            ->flatten()
            ->filter()
            ->unique()
            ->toArray();
    }

    public function subscribe(string $email): bool
    {
        $response = $this->client->subscribe($email);

        return $response !== false && $response !== null;
    }
}
