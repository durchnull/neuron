<?php

namespace Tests\Feature\Api\Engine\Customer;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Exception
     */
    public function test_customer_create(): void
    {
        $merchant = $this->actionMerchantCreate();

        $salesChannelCreateResponse = $this->apiSalesChannelCreate($merchant->token);

        $salesChannelToken = $salesChannelCreateResponse->json()['data']['token'];

        $customerCreateResponse = $this->apiCustomerCreate(
            $salesChannelToken,
            'customer@domain.de',
            'John Doe'
        );

        $customerCreateResponse->assertStatus(201);
        $this->assertEquals('customer@domain.de', $customerCreateResponse->json()['data']['email']);
        $this->assertEquals('John Doe', $customerCreateResponse->json()['data']['full_name']);
        $this->assertTrue($customerCreateResponse->json()['data']['new']);
        $this->assertEquals(0, $customerCreateResponse->json()['data']['order_count']);
    }
}
