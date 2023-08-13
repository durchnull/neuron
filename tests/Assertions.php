<?php

namespace Tests;

trait Assertions
{
    // <editor-fold desc="Assertion">

    protected function assertOrderTotals(
        array $orderResource,
        int $amount,
        int $itemsAmount,
        int $itemsDiscountAmount,
        int $shippingAmount,
        int $shippingDiscountAmount,
    ): void {
        $this->assertEquals($amount, $orderResource['amount']);
        $this->assertEquals($itemsAmount, $orderResource['items_amount']);
        $this->assertEquals($itemsDiscountAmount, $orderResource['items_discount_amount']);
        $this->assertEquals($shippingAmount, $orderResource['shipping_amount']);
        $this->assertEquals($shippingDiscountAmount, $orderResource['shipping_discount_amount']);
    }

    // </editor-fold>
}
