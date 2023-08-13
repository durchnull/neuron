<?php

namespace App\Actions\Engine\Coupon;

class CouponCreateAction extends CouponAction
{
    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid',
            'rule_id' => 'required|uuid|exists:rules,id',
            'name' => 'required|string',
            'code' => 'required',
            'enabled' => 'required|bool',
            'combinable' => 'required|bool',
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
