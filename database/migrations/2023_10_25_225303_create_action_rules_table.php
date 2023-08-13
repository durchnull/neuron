<?php

use App\Actions\Engine\Order\OrderAddItemAction;
use App\Actions\Engine\Order\OrderCreateAction;
use App\Actions\Engine\Order\OrderPlaceAction;
use App\Actions\Engine\Order\OrderRedeemCouponAction;
use App\Actions\Engine\Order\OrderRemoveCouponAction;
use App\Actions\Engine\Order\OrderRemoveItemAction;
use App\Actions\Engine\Order\OrderUpdateCustomerAction;
use App\Actions\Engine\Order\OrderUpdateItemAction;
use App\Actions\Engine\Order\OrderUpdateItemQuantityAction;
use App\Actions\Engine\Order\OrderUpdatePaymentAction;
use App\Actions\Engine\Order\OrderUpdateShippingAction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('action_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sales_channel_id')
                ->constrained();
            $table->foreignUuid('condition_id')
                ->constrained();
            $table->string('name');
            $table->enum('action', array_map(fn(string $class) => class_basename($class), [
                OrderCreateAction::class,
                OrderAddItemAction::class,
                OrderRemoveItemAction::class,
                OrderUpdateItemAction::class,
                OrderUpdateItemQuantityAction::class,
                OrderUpdateShippingAction::class,
                OrderUpdatePaymentAction::class,
                OrderUpdateCustomerAction::class,
                OrderRedeemCouponAction::class,
                OrderRemoveCouponAction::class,
                OrderPlaceAction::class,
            ]));
            $table->boolean('enabled')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('action_rules');
    }
};
