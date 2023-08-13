<?php

use App\Enums\Order\OrderStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sales_channel_id')
                ->constrained();
            $table->foreignUuid('shipping_id')
                ->constrained();
            $table->foreignUuid('payment_id')
                ->nullable()
                ->constrained();
            $table->foreignUuid('customer_id')
                ->nullable()
                ->constrained();
            $table->foreignUuid('billing_address_id')
                ->nullable()
                ->constrained('addresses')
                ->cascadeOnDelete();
            $table->foreignUuid('shipping_address_id')
                ->nullable()
                ->constrained('addresses')
                ->cascadeOnDelete();
            $table->unsignedInteger('amount')->default(0);
            $table->unsignedInteger('items_amount')->default(0);
            $table->unsignedInteger('items_discount_amount')->default(0);
            $table->unsignedInteger('shipping_amount')->default(0);
            $table->unsignedInteger('shipping_discount_amount')->default(0);
            $table->string('order_number');
            $table->unsignedInteger('version')->default(1);
            $table->enum('status', array_map(fn(OrderStatusEnum $statusEnum) => $statusEnum->value, OrderStatusEnum::cases()))->default(OrderStatusEnum::Open);
            $table->string('customer_note')->nullable();
            $table->timestamp('ordered_at', 0)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
