<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_billbee', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sales_channel_id')
                ->constrained();
            $table->boolean('enabled')->default(false);
            $table->boolean('receive_inventory')->default(false);
            $table->boolean('distribute_order')->default(false);
            $table->string('name');
            $table->string('user');
            $table->string('api_password');
            $table->string('api_key');
            $table->string('shop_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_billbee');
    }
};
