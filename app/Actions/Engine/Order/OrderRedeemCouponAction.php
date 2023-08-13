<?php

namespace App\Actions\Engine\Order;

use App\Enums\Order\OrderStatusEnum;
use App\Enums\Order\PolicyReasonEnum;
use App\Facades\Order;
use App\Facades\Coupon;
use App\Facades\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class OrderRedeemCouponAction extends OrderAction
{
    protected ?\App\Models\Engine\Coupon $coupon;

    public static function rules(): array
    {
        return [
            'order_id' => 'required|uuid|exists:orders,id',
            'code' => 'required|exists:coupons,code,enabled,1'
        ];
    }

    protected function gate(array $attributes): void
    {
        parent::gate($attributes);

        // @todo [test]
        if ($this->target->coupons->contains(fn(\App\Models\Engine\Coupon $coupon) => $coupon->code === $this->validated['code'])) {
            $this->addPolicy(PolicyReasonEnum::CouponIsRedeemed);
        } else {
            $this->coupon = Coupon::getByCode($this->validated['code']);
        }

        // @todo [test]
        if (!$this->coupon) {
            throw new ModelNotFoundException();
        }

        if (! $this->coupon->combinable && $this->target->coupons->contains(fn(\App\Models\Engine\Coupon $coupon) => !$coupon->combinable)) {
            $this->addPolicy(PolicyReasonEnum::CouponIsNotCombinable);
        }
    }

    public static function afterState(): array
    {
        return [OrderStatusEnum::Open];
    }

    protected function apply(): void
    {
        $coupon = $this->coupon;

        $this->target->coupons()->attach($coupon);
        $this->target->setRelation(
            'coupons',
            collect([$coupon])->concat($this->target->coupons->where('id', '!=', $coupon->id))
        );

        $this->target = Rule::apply($this->target);

        $this->target->update(
            array_merge(Order::getTotals($this->target), [
                'version' => $this->target->version + 1
            ])
        );

        $this->target = Order::updatePayment($this->target);
    }
}
