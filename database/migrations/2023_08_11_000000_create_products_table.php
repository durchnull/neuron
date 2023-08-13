<?php

use App\Enums\Product\ProductTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sales_channel_id')
                ->constrained();
            $table->string('inventoryable_type');
            $table->uuid('inventoryable_id');
            $table->string('inventory_id')->nullable(); // @todo not nullable
            $table->boolean('enabled');
            $table->string('name');
            $table->enum('type', [
                ProductTypeEnum::Product->value,
                ProductTypeEnum::Bundle->value,
            ]);
            $table->string('sku');
            $table->string('ean')->nullable();
            $table->unsignedInteger('net_price');
            $table->unsignedInteger('gross_price');
            $table->json('configuration')->nullable();
            $table->string('url')->nullable();
            $table->string('image_url')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
