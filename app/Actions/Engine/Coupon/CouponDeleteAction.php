<?php

namespace App\Actions\Engine\Coupon;

class CouponDeleteAction extends CouponAction
{
    public static function rules(): array
    {
        return [];
    }

    protected function gate(array $attributes): void
    {
        // @todo doesnt belong to any orders

        // rule > condition
    }

    protected function apply(): void
    {
        // @todo delete rule if it only belongs to target

        $this->target->delete();
    }
}
