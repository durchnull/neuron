<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sales_channel_id')
                ->constrained();
            $table->foreignUuid('rule_id')
                ->constrained();
            $table->string('name');
            $table->string('code');
            $table->boolean('enabled')->default(false);
            $table->boolean('combinable')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
