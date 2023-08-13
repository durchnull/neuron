<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shippings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sales_channel_id')
                ->constrained();
            $table->boolean('enabled');
            $table->string('name');
            $table->char('country_code', 2);
            $table->unsignedInteger('net_price');
            $table->unsignedInteger('gross_price');
            $table->string('currency_code');
            $table->unsignedTinyInteger('position');
            $table->boolean('default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shippings');
    }
};
