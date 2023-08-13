<?php

namespace Tests\Feature\Api\Engine\Cart;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefundTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_refunding(): void
    {
        $this->markTestIncomplete('No implementation');
    }

    public function test_order_can_refund_deposit_of_condition(): void
    {
        $this->markTestIncomplete('No implementation');
    }

    public function test_order_can_not_refund_transaction_if_payment_method_was_free(): void
    {
        $this->markTestIncomplete('No implementation');
    }
}
