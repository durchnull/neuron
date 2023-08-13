<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignUuid('product_id')
                ->constrained();
            $table->uuid('reference')->nullable();
            $table->unsignedInteger('product_version');
            $table->unsignedMediumInteger('total_amount');
            $table->unsignedMediumInteger('unit_amount');
            $table->unsignedMediumInteger('discount_amount');
            $table->unsignedSmallInteger('quantity');
            $table->unsignedSmallInteger('position');
            $table->json('configuration')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
