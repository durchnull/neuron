<?php

namespace App\Actions\Engine\Order;

use App\Enums\Order\OrderStatusEnum;
use App\Enums\Order\PolicyReasonEnum;
use App\Facades\Order;
use App\Facades\Coupon;
use App\Facades\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderRemoveCouponAction extends OrderAction
{
    protected ?\App\Models\Engine\Coupon $coupon;

    public static function rules(): array
    {
        return [
            'order_id' => 'required|uuid|exists:orders,id',
            'code' => 'required|string'
        ];
    }

    protected function gate(array $attributes): void
    {
        parent::gate($attributes);

        $this->coupon = Coupon::getByCode($this->validated['code']);

        if (!$this->coupon) {
            throw new ModelNotFoundException();
        }

        // @todo [test]
        if (!$this->target->coupons->contains(fn(\App\Models\Engine\Coupon $coupon) => $coupon->id === $this->coupon->id)) {
            $this->addPolicy(PolicyReasonEnum::ModelNotFound);
        }
    }

    public static function afterState(): array
    {
        return [OrderStatusEnum::Open];
    }

    protected function apply(): void
    {
        if ($this->coupon instanceof \App\Models\Engine\Coupon) {
            $this->target->coupons()->detach($this->coupon);
            $this->target->setRelation('coupons', collect()->concat($this->target->coupons->where('id', '!=', $this->coupon->id)));

            $this->target = Rule::apply($this->target);

            $this->target->update(
                array_merge(Order::getTotals($this->target), [
                    'version' => $this->target->version + 1
                ])
            );

            $this->target = Order::updatePayment($this->target);
        }
    }
}
