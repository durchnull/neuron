<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_channels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('merchant_id')
                ->constrained();
            $table->string('token');
            $table->string('cart_token');
            $table->json('domains');
            $table->string('name');
            $table->string('currency_code');
            $table->string('locale');
            $table->boolean('use_stock');
            $table->boolean('remove_items_on_price_increase');
            $table->string('checkout_summary_url');
            $table->string('order_summary_url');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_channels');
    }
};
