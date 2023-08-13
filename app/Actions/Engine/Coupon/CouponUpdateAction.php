<?php

namespace App\Actions\Engine\Coupon;

class CouponUpdateAction extends CouponAction
{
    public static function rules(): array
    {
        return [
            'rule_id' => 'nullable|uuid|exists:rules,id',
            'name' => 'nullable|string',
            'code' => 'nullable|string',
            'enabled' => 'nullable|bool',
            'combinable' => 'nullable|bool',
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
