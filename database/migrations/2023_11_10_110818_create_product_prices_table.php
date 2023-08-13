<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')
                ->constrained();
            $table->unsignedInteger('net_price');
            $table->unsignedInteger('gross_price');
            $table->boolean('enabled')->nullable();
            $table->timestamp('begin_at', 0)->nullable();
            $table->timestamp('end_at', 0)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
