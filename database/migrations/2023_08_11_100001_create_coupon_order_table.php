<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupon_order', function (Blueprint $table) {
            $table->foreignUuid('coupon_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignUuid('order_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamp('created_at', 0)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_order');
    }
};
