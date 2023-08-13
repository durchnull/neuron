<?php

namespace App\Services\Engine;

use App\Actions\Engine\Rule\RuleUpdateAction;
use App\Consequence\Consequence;
use App\Consequence\Credit;
use App\Consequence\Presets\CreditOnAllProducts;
use App\Contracts\Engine\CouponServiceContract;
use App\Contracts\Engine\SalesChannelContract;
use App\Enums\TriggerEnum;
use App\Generators\CouponCodeGenerator;
use App\Models\Engine\Coupon;
use App\Models\Engine\Order;
use Exception;

class CouponService implements CouponServiceContract
{
    public function __construct(
        protected SalesChannelContract $salesChannelService,
        protected CouponCodeGenerator $couponCodeGenerator
    ) {
    }

    /**
     * @return string
     * @throws Exception
     */
    public function generateCode(): string
    {
        return $this->couponCodeGenerator->generate();
    }

    /**
     * @param  string  $code
     * @return Coupon|null
     * @throws Exception
     */
    public function getByCode(string $code): ?Coupon
    {
        $coupon = Coupon::where([
            'sales_channel_id' => $this->salesChannelService->id(),
            'code' => $code
        ])->first();

        if ($coupon->code !== $code) {
            // @todo [reconsider]
            throw new Exception('Coupon code mismatch');
        }

        return $coupon;
    }

    /**
     * @throws Exception
     */
    public function chargeCredit(Order $order): Order
    {
        /*
        // @todo [coupon] simplistic model for one case for now: assuming all discounts come from one specific rule (non-percentage discount on all products)
        foreach ($order->coupons as $coupon) {
            foreach ($coupon->rule->consequences->toArray() as $consequence) {
                if ($consequence[0] === Credit::getType()) {

                    $action = new RuleUpdateAction($coupon->rule, [
                        'consequences' => CreditOnAllProducts::make(9000)->toArray(),
                    ], TriggerEnum::App);

                    $action->trigger();
                    $order->load('coupons');
                    break;
                }
            }
        }
        */

        return $order;
    }
}
