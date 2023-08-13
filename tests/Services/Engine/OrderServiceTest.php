<?php

namespace Tests\Services\Engine;

use App\Enums\Order\OrderStatusEnum;
use App\Facades\Rule;
use App\Facades\SalesChannel;
use App\Models\Engine\CartRule;
use App\Models\Engine\Coupon;
use App\Models\Engine\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_new()
    {
        $order = new Order();

        $this->assertEquals(1, $order->version);
        $this->assertEquals(OrderStatusEnum::Open, $order->status);
    }

    public function test_get_rules()
    {
        $this->markTestIncomplete('Rules need to be implemented in RuleService');

        $order = Order::factory()
            ->state([
                'status' => OrderStatusEnum::Open->value
            ])
            ->create();

        SalesChannel::set($order->salesChannel);

        $this->assertEmpty(Rule::getSortedRules($order));

        CartRule::factory()
            ->state([
                'sales_channel_id' => $order->sales_channel_id
            ])
            ->count(10)
            ->enabled()
            ->create();

        $order = \App\Facades\Order::update($order);

        $this->assertDatabaseCount('coupons', 0);
        $this->assertDatabaseCount('rules', 10);
        $this->assertCount(10, Rule::getSortedRules($order));

        $coupons = Coupon::factory()
            ->state([
                'sales_channel_id' => $order->sales_channel_id
            ])
            ->count(5)
            ->enabled()
            ->create();

        $order->coupons()->saveMany($coupons);

        $order->load('coupons.rule');

        $this->assertDatabaseCount('coupons', 5);
        $this->assertDatabaseCount('rules', 15);
        $this->assertCount(15, Rule::getSortedRules($order));
    }
}
