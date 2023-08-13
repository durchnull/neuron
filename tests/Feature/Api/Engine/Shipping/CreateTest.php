<?php

namespace Tests\Feature\Api\Engine\Shipping;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_shipping_create(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];

        $shippingCreateResponse = $this->apiShippingCreate($salesChannelToken, [
                'enabled' => true,
                'name' => 'Shippings Provider',
                'country_code' => 'DE',
                'net_price' => 300,
                'gross_price' => 400,
                'currency_code' => 'EUR',
                'position' => 0,
            ]);

        $shippingCreateResponse->assertStatus(201);
    }
}
